<?php

namespace App\Console\Commands;

use App\Imports\SettlementsImport;
use App\Models\FederalEntity;
use App\Models\Municipality;
use App\Models\Settlement;
use App\Models\SettlementType;
use App\Models\ZipCode;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class UploadSettlements extends Command
{

    /**
     * Column names in the spreadsheet
     * 
     */
    protected $columns = [
        0 => "d_codigo",
        1 => "d_asenta",
        2 => "d_tipo_asenta",
        3 => "D_mnpio",
        4 => "d_estado",
        5 => "d_ciudad",
        6 => "d_CP",
        7 => "c_estado",
        8 => "c_oficina",
        9 => "c_CP",
        10 => "c_tipo_asenta",
        11 => "c_mnpio",
        12 => "id_asenta_cpcons",
        13 => "d_zona",
        14 => "c_cve_ciudad"
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:settlements {name} {key} {csvfile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload SettlementsImport by Federal Entity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         * Federal Entity
         */
        $this->line("Finding or Creating Federal Entity");

        $federal_entity_fields = [
            'name' => mb_strtoupper($this->argument('name')), 
            'key' => $this->argument('key') 
        ];
        $federal_entity = FederalEntity::firstOrCreate($federal_entity_fields, $federal_entity_fields);
        $this->info("Federal Entity: {$federal_entity->name}");

        /**
         * Excel Load
         */
        $this->line("Loading CSV File");
        $collection = Excel::toCollection(new SettlementsImport, storage_path($this->argument('csvfile')));
        $slice = $collection->first()->slice(1);
        $this->info("Load completed, total: {$slice->count()} - Upload Begin");

        $this->withProgressBar($slice, function ($row) use ($federal_entity) {
            /**
             * Mapping Columns with Values
             */
            $item = collect(array_combine($this->columns, $row->toArray()));

            /**
             * Municipality
             */
            $municipality_fields = [
                'name' => mb_strtoupper($item->get('D_mnpio')), 
                'key' => $item->get('c_mnpio')
            ];
            $municipality = Municipality::firstOrCreate($municipality_fields, $municipality_fields);
    
            /**
             * Saving ZipCode with Municipality and Federal Entity
             */
            $zip_code_fields = [
                'zip_code' => $item->get('d_codigo'),
                'locality' => mb_strtoupper($item->get('d_ciudad')),
                'municipality_id' => $municipality->id,
                'federal_entity_id' => $federal_entity->id
            ];

            $zip_code = ZipCode::firstOrCreate($zip_code_fields);

            /**
             * Saving Settlement Type
             */
            $settlement_type_fields = [
                'name' => $item->get('d_tipo_asenta'),
            ];

            $settlement_type = SettlementType::firstOrCreate($settlement_type_fields);

            /**
             * Saving Settlement with Zip Code Relation
             */
            $settlement_fields = [
                'key' => $item->get('id_asenta_cpcons'),
                'name' => mb_strtoupper($item->get('d_asenta')),
                'zone_type' => mb_strtoupper($item->get('d_zona')),
                'settlement_type_id' => $settlement_type->id
            ];

            $settlement = Settlement::firstOrCreate($settlement_fields);
            $zip_code->settlements()->attach($settlement->id);
        });
        $this->newLine(2);

        return Command::SUCCESS;
    }
}
