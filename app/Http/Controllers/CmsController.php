<?php
namespace App\Http\Controllers;

use App\Models\SiteContent;
use App\Support\ImageAsset;
use App\Support\RichText;
use App\Support\SiteLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CmsController
{
    private function authorize(Request $request): void { abort_unless($request->user()?->is_admin, 403); }

    private function definition(string $page, ?string $block): array
    {
        $pages = config('cms.pages');
        abort_unless(isset($pages[$page]), 404);
        $block ??= array_key_first($pages[$page]['blocks']);
        abort_unless(isset($pages[$page]['blocks'][$block]), 404);
        return [$pages, $pages[$page], $block, $pages[$page]['blocks'][$block]];
    }

    private function values(array $definition, string $locale): array
    {
        $site = SiteContent::allSections($locale); $values = [];
        foreach ($definition['fields'] as $id => $field) $values[$id] = data_get($site, $field['bind']);
        return $values;
    }

    public function index(Request $request)
    {
        $this->authorize($request);
        return view('admin.dashboard', ['pages' => config('cms.pages'), 'pageKey' => null, 'title' => 'Vaše stranice', 'locale' => SiteLocale::cms($request)]);
    }

    public function edit(Request $request, string $page, ?string $block = null)
    {
        $this->authorize($request);
        [$pages, $pageData, $block, $definition] = $this->definition($page, $block);
        $locale = SiteLocale::cms($request);
        $values = $this->values($definition, $locale);
        $pageData['url'] = SiteLocale::url(isset(SiteLocale::PATHS[$locale][$page]) ? $page : 'home', $locale);
        return view('admin.page', ['pages'=>$pages,'pageKey'=>$page,'page'=>$pageData,'blockKey'=>$block,'block'=>$definition,'values'=>$values,'revision'=>hash('sha256',json_encode($values)),'title'=>$pageData['label'],'locale'=>$locale]);
    }

    private function rules(array $field): array
    {
        $rules = [($field['required'] ?? false) ? 'required' : 'nullable'];
        return array_merge($rules, match ($field['type']) {
            'number' => ['numeric', 'min:0', 'max:100000'],
            'boolean' => ['boolean'],
            'date' => ['date_format:Y-m-d'],
            'ga4' => ['string','regex:/^G-[A-Z0-9]+$/','max:32'],
            'token' => ['string','regex:~^[a-zA-Z0-9_+=/.-]+$~','max:255'],
            'url' => ['url:http,https','max:1000'],
            'origin' => ['url:http,https','max:255',function ($attribute,$value,$fail) {
                $parts=parse_url($value);
                if (isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment']) || !in_array($parts['path'] ?? '', ['', '/'])) $fail('Unesite samo domenu, bez putanje ili parametara.');
            }],
            'maps' => ['url:https','max:2000',function ($attribute,$value,$fail) {
                $parts=parse_url($value);
                if (!in_array($parts['host'] ?? '', ['www.google.com','maps.google.com','www.google.hr','maps.google.hr']) || !str_starts_with($parts['path'] ?? '', '/maps/embed') || isset($parts['user']) || isset($parts['pass'])) $fail('Unesite valjani URL ugrađene Google Maps karte.');
            }],
            'email' => ['email','max:254'],
            'phone' => ['string','regex:/^[+0-9 ()-]{5,30}$/'],
            'oib' => ['string','regex:/^\d{11}$/'],
            'select','icon' => ['string',Rule::in(array_keys($field['options']))],
            'image' => ['string','max:255',function ($attribute,$value,$fail) { if ($value && !ImageAsset::allowed($value)) $fail('Odaberite fotografiju sa stranice ili učitajte novu.'); }],
            'rich','legal' => ['string','max:50000'],
            'textarea' => ['string','max:15000'],
            default => ['string','max:1000'],
        });
    }

    public function update(Request $request, string $page, string $block)
    {
        $this->authorize($request);
        [, , , $definition] = $this->definition($page, $block);
        $locale = SiteLocale::cms($request);
        $current = $this->values($definition, $locale);
        $rules = ['revision'=>['required','string','size:64'], 'fields'=>['required','array:'.implode(',',array_keys($definition['fields']))]];
        $labels = [];
        $input = $request->input('fields', []);
        foreach ($definition['fields'] as $id=>$field) {
            $path = 'fields.'.$id;
            if ($field['type'] === 'repeater') {
                if (array_key_exists($id, $input) && ($input[$id] === '' || $input[$id] === null)) $input[$id] = [];
                $rules[$path] = ['present','array','min:'.($field['min'] ?? 0),'max:'.$field['max']];
                $rules[$path.'.*'] = ['array:'.implode(',',array_keys($field['fields']))];
                foreach ($field['fields'] as $key=>$child) {
                    $rules[$path.'.*.'.$key] = $this->rules($child);
                    $labels[$path.'.*.'.$key] = $child['label'];
                    if ($child['type'] === 'image') {
                        $rules[$path.'.*.'.$key][0] = 'nullable';
                        $rules['uploads.'.$id.'.*.'.$key] = ['nullable','file','mimes:jpg,jpeg,png,webp','max:10240','dimensions:max_width=10000,max_height=10000'];
                    }
                }
            } else {
                $rules[$path] = $this->rules($field);
                if ($field['type'] === 'image') {
                    $rules[$path][0] = 'nullable';
                    $rules['uploads.'.$id] = ['nullable','file','mimes:jpg,jpeg,png,webp','max:10240','dimensions:max_width=10000,max_height=10000'];
                }
            }
            $labels[$path] = $field['label'];
        }
        $request->merge(['fields'=>$input]);
        $validated = $request->validate($rules, [
            'required'=>'Polje :attribute je obavezno.', 'email'=>'Upišite ispravnu e-mail adresu.',
            'max'=>'Polje :attribute prelazi dopuštenu veličinu (:max).', 'min'=>'Provjerite minimalnu vrijednost u polju :attribute (:min).',
            'mimes'=>'Učitajte JPG, PNG ili WebP fotografiju.', 'date_format'=>'Upišite ispravan datum.', 'regex'=>'Provjerite format polja :attribute.',
        ], $labels);
        if ($page === 'pricing') {
            foreach ($validated['fields']['items'] as $index=>$row) {
                if (($row['anchor_amount'] ?? null) !== null && $row['anchor_amount'] !== '') {
                    if (($row['amount'] ?? null) === null || $row['amount'] === '') throw ValidationException::withMessages(['fields.items.'.$index.'.amount'=>'Uz sidrenu cijenu upišite i aktualnu cijenu.']);
                    if ((float)$row['anchor_amount'] < (float)$row['amount']) throw ValidationException::withMessages(['fields.items.'.$index.'.anchor_amount'=>'Sidrena cijena mora biti jednaka ili veća od aktualne.']);
                }
            }
        }
        if (!hash_equals(hash('sha256',json_encode($current)), $validated['revision'])) throw ValidationException::withMessages(['revision'=>'Ova je sekcija u međuvremenu promijenjena. Otvorite je ponovno prije spremanja.']);
        $created = []; $updates = [];
        try {
            foreach ($definition['fields'] as $id=>$field) {
                $value = $validated['fields'][$id] ?? null;
                if ($field['type'] === 'repeater') {
                    $rows = [];
                    foreach ($value as $index=>$row) {
                        $clean = [];
                        foreach ($field['fields'] as $key=>$child) $clean[$key] = $this->normalize($child,$row[$key] ?? null,$request,'uploads.'.$id.'.'.$index.'.'.$key,'fields.'.$id.'.'.$index.'.'.$key,$created);
                        $rows[]=$clean;
                    }
                    $value=$rows;
                } else $value=$this->normalize($field,$value,$request,'uploads.'.$id,'fields.'.$id,$created);
                [$section,$path] = array_pad(explode('.',$field['bind'],2),2,null);
                $storageLocale = SiteContent::sharedField($section, $path) ? 'hr' : $locale;
                $storageKey = SiteContent::storageKey($section, $storageLocale);
                $updates[$storageKey] ??= SiteContent::section($section, $storageLocale);
                if ($path === null) $updates[$storageKey] = $value;
                else Arr::set($updates[$storageKey],$path,$value);
            }
            DB::transaction(function () use ($updates) { foreach ($updates as $key=>$value) SiteContent::updateOrCreate(['key'=>$key],['value'=>$value]); });
        } catch (\Throwable $error) {
            foreach ($created as $file) if (is_file($file)) unlink($file);
            throw $error;
        }
        return redirect(SiteLocale::adminUrl('/admin/pages/'.$page.'/'.$block, $locale))->with('saved','Promjene su spremljene i vidljive na stranici.');
    }

    private function normalize(array $field, mixed $value, Request $request, string $upload, string $attribute, array &$created): mixed
    {
        if ($field['type'] === 'image') {
            if ($request->hasFile($upload)) return ImageAsset::upload($request->file($upload), $attribute, $created);
            if (!$value && ($field['required'] ?? false)) throw ValidationException::withMessages([$attribute=>'Odaberite fotografiju.']);
            return $value ?? '';
        }
        if (in_array($field['type'], ['rich','legal'])) {
            $clean = RichText::clean($value ?? '');
            if (($field['required'] ?? false) && trim(strip_tags($clean)) === '') throw ValidationException::withMessages([$attribute=>'Upišite sadržaj.']);
            return $clean;
        }
        if ($field['type'] === 'boolean') return (bool) $value;
        if ($field['type'] === 'number') return $value === null || $value === '' ? null : (float) $value;
        return $value ?? '';
    }
}
