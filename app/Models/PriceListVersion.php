<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use XMLWriter;

class PriceListVersion extends Model
{
    public $timestamps = false;
    protected $fillable = ['locale', 'content_hash', 'payload', 'published_at'];
    protected function casts(): array { return ['payload' => 'array', 'published_at' => 'immutable_datetime']; }

    public function filename(): string
    {
        $object = $this->payload['object'];
        return implode('_', [Str::slug($object['type']), Str::slug($object['address']), Str::slug($object['code']), $this->id, $this->published_at->timezone('Europe/Zagreb')->format('Y-m-d_H-i-s'), $this->locale]).'.xml';
    }

    public function xml(): string
    {
        $data = $this->payload;
        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('cjenik_usluga');
        $xml->writeAttribute('verzija', '1.0');
        $xml->writeAttribute('jezik', $this->locale);
        $xml->writeAttribute('valuta', 'EUR');
        $xml->writeElement('broj_pohrane', (string) $this->id);
        $xml->writeElement('datum_objave', $this->published_at->timezone('Europe/Zagreb')->toIso8601String());
        $xml->writeElement('vrijedi_od', $data['valid_from']);
        $xml->startElement('pruzatelj_usluge');
        $xml->writeElement('naziv', $data['business']['name']);
        $xml->writeElement('oib', $data['business']['oib']);
        $xml->endElement();
        $xml->startElement('usluzni_objekt');
        $xml->writeElement('vrsta', $data['object']['type']);
        $xml->writeElement('adresa', $data['object']['address']);
        $xml->writeElement('oznaka', $data['object']['code']);
        $xml->endElement();
        $xml->startElement('usluge');
        foreach ($data['items'] as $item) {
            $xml->startElement('usluga');
            $xml->writeElement('naziv_usluge', $item['name']);
            $xml->writeElement('kategorija', $item['category']);
            $xml->writeElement('opis', $item['detail']);
            $xml->writeElement('maloprodajna_cijena', $item['amount']);
            $xml->writeElement('pocetna_cijena', $item['from'] ? 'true' : 'false');
            $xml->startElement('posebni_oblik_prodaje');
            $xml->writeElement('primijenjen', $item['special_sale'] ? 'true' : 'false');
            $xml->writeElement('naziv', $item['special_sale_name']);
            $xml->endElement();
            $xml->startElement('sidrena_cijena');
            $xml->writeAttribute('datum', $item['anchor_date']);
            $xml->text($item['anchor_amount']);
            $xml->endElement();
            $xml->endElement();
        }
        $xml->endElement();
        $xml->endElement();
        $xml->endDocument();
        return $xml->outputMemory();
    }
}
