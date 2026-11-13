@php
    $parts=explode('.',$path); $name=array_shift($parts).'['.implode('][',$parts).']'; $id='cms-'.str_replace('.','-',$path);
    $uploadPath=preg_replace('/^fields\./','uploads.',$path); $uploadParts=explode('.',$uploadPath); $uploadName=array_shift($uploadParts).'['.implode('][',$uploadParts).']';
    $type=$field['type']; $rich=in_array($type,['rich','legal']);
@endphp
@if($type==='repeater')
<section class="repeater-field" data-repeater data-next-index="{{ is_array($value) && count($value) ? max(array_keys($value))+1 : 0 }}" data-item-name="{{ $field['item'] }}" data-max="{{ $field['max'] }}">
    <div class="repeater-heading"><div><h3>{{ $field['label'] }} <span data-item-count>{{ count(is_array($value)?$value:[]) }}</span></h3><p>Otvorite stavku za uređivanje. Redoslijed možete promijeniti tipkama gore i dolje.</p></div><button class="admin-button secondary" type="button" data-add-item><x-icon name="plus" /> {{ $field['add'] }}</button></div>
    <input type="hidden" name="{{ $name }}" value="">
    <div data-repeat-list>
        @foreach(is_array($value)?$value:[] as $index=>$item)
            @include('admin.partials.row',['rowField'=>$field,'rowPath'=>$path.'.'.$index,'item'=>$item,'index'=>$index,'template'=>false,'open'=>$loop->first || $errors->has($path.'.'.$index.'.*')])
        @endforeach
    </div>
    <p class="repeater-empty" data-empty @if(count(is_array($value)?$value:[])) hidden @endif>Nema dodanih stavki. Dodajte prvu.</p>
    <template data-repeat-template>@include('admin.partials.row',['rowField'=>$field,'rowPath'=>$path.'.__INDEX__','item'=>[],'index'=>'__INDEX__','template'=>true,'open'=>true])</template>
    <button class="admin-add-bottom" type="button" data-add-item><x-icon name="plus" /> {{ $field['add'] }}</button>
    @error($path)<span class="field-error">{{ $message }}</span>@enderror
</section>
@else
<div @class(['editor-field','field-wide'=>$rich || in_array($type,['image','textarea','icon'])])>
    <label id="{{ $id }}-label" for="{{ $id }}{{ $rich ? '-editor' : ($type==='image' ? '-file' : '') }}">{{ $field['label'] }}@if($field['required'] ?? false)<span class="required-mark" aria-label="obavezno">*</span>@endif</label>
    @if($rich)
        @php
            $editorValue=\App\Support\RichText::html((string)$value,$type==='legal');
            $contact=\App\Models\SiteContent::section('contact');
            $editorValue=strtr($editorValue,['{phone}'=>e($contact['phone']),'{email}'=>e($contact['email']),'{address}'=>e($contact['address']),'{city}'=>e($contact['city'])]);
        @endphp
        <div class="rich-editor" data-rich-editor>
            <div class="editor-mode"><button type="button" class="is-active" data-rich-mode="visual" aria-pressed="true">Vizualni editor</button><button type="button" data-rich-mode="html" aria-pressed="false">HTML</button></div>
            <input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{ $editorValue }}" data-rich-input>
            <trix-toolbar id="{{ $id }}-toolbar"></trix-toolbar>
            <trix-editor id="{{ $id }}-editor" input="{{ $id }}" toolbar="{{ $id }}-toolbar" class="trix-content" aria-label="{{ $field['label'] }}" data-rich-visual></trix-editor>
            <textarea class="html-source" aria-label="HTML — {{ $field['label'] }}" data-rich-source hidden spellcheck="false">{{ $editorValue }}</textarea>
        </div>
    @elseif($type==='image')
        <div class="image-field" data-image-field>
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            <div class="image-preview">@if($value)<img src="{{ \App\Support\ImageAsset::url($value) }}" alt="Trenutačna fotografija" data-image-preview data-original-src="{{ \App\Support\ImageAsset::url($value) }}">@else<img alt="Nova fotografija" data-image-preview data-original-src="" hidden>@endif<span data-image-empty @if($value) hidden @endif><x-icon name="image" /> Odaberite fotografiju</span></div>
            <div class="image-actions"><input type="file" name="{{ $uploadName }}" id="{{ $id }}-file" accept="image/jpeg,image/png,image/webp" data-image-input><button type="button" class="admin-button secondary" data-image-choose><x-icon name="image" /> {{ $value ? 'Zamijeni fotografiju' : 'Odaberi fotografiju' }}</button><button class="admin-button ghost" type="button" data-image-reset hidden>Poništi zamjenu</button><p>JPG, PNG ili WebP · do 10 MB i 24 MP.<br>Fotografije se automatski optimiziraju.</p><span data-image-filename class="image-filename"></span></div>
        </div>
        @error($uploadPath)<span class="field-error">{{ $message }}</span>@enderror
    @elseif($type==='icon')
        <div class="icon-picker" data-icon-picker>
            <input type="search" placeholder="Pronađi ikonu…" aria-label="Pretraži ikone" data-icon-search>
            <div class="icon-picker-grid" role="radiogroup" aria-labelledby="{{ $id }}-label">
            @foreach($field['options'] as $option=>$label)
                <label class="icon-choice" data-icon-option data-icon-label="{{ $label.' '.$option }}"><input type="radio" name="{{ $name }}" value="{{ $option }}" @checked($value===$option) data-service-icon><span><x-icon :name="$option" /><span>{{ $label }}</span></span></label>
            @endforeach
            </div>
            <p class="field-help" data-icon-empty hidden>Nema ikona za taj pojam.</p>
        </div>
    @elseif($type==='select' || $type==='boolean')
        <select name="{{ $name }}" id="{{ $id }}" @if($type==='select') data-icon-select @endif>
        @foreach($type==='boolean' ? ($field['options'] ?? [0=>'Točan iznos',1=>'Početna cijena — od']) : $field['options'] as $option=>$label)<option value="{{ $option }}" @selected((string)$value===(string)$option)>{{ $label }}</option>@endforeach
        </select>
    @elseif($type==='textarea')
        <textarea name="{{ $name }}" id="{{ $id }}" rows="4">{{ $value }}</textarea>
    @else
        <input name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" type="{{ in_array($type,['email','date','number'])?$type:'text' }}" @if($type==='number') min="0" max="100000" step="0.01" placeholder="{{ $field['placeholder'] ?? 'Na upit' }}" @elseif($type==='oib') inputmode="numeric" maxlength="11" @else maxlength="1000" placeholder="{{ $field['placeholder'] ?? '' }}" @endif @if(in_array(end($parts),['title','name','question','alt'])) data-row-title-input @endif>
    @endif
    @if(!empty($field['help']))<p class="field-help">{{ $field['help'] }}</p>@endif
    @error($path)<span class="field-error">{{ $message }}</span>@enderror
</div>
@endif
