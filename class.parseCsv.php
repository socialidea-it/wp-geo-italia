<?php

use League\Csv\Reader;
use League\Csv\Writer;

class parseCsv {

    public function generateDataSource() {
        
        $csv = Reader::createFromPath( __DIR__.'/database.original.csv' , 'r');
        $csv->setHeaderOffset(0);

        $rows = [];
        $data = [];
        $temp = [];
        $n = 0;
        foreach ( $csv as $record ) {
            $comune = $record['Comune'];
            $cap = $record['Cap'];
            $provincia = getProvincia( $record['Provincia'] );
            $provincia_sigla = $record['Provincia'];
            $regione = $record['Regione'];
            if( count( $temp ) > 0 &&
                array_key_exists( $provincia_sigla, $temp ) && 
                in_array( $comune, $temp[ $provincia_sigla ] ) 
            ) continue;
            $temp[ $provincia_sigla ][] = $comune;
            $rows[ $regione ][ $provincia ][] = [ $comune, $cap, $provincia, $provincia_sigla, $regione ];
            $n++;
        }

        foreach( $rows as $regioni ) {
            foreach( $regioni as $provincie ) {
                foreach( $provincie as $comune ) {
                    $data[] = $comune;
                }
            }
        }

        $header = ['comune','cap','provincia','provincia_sigla','regione'];
        $csv = Writer::createFromString();
        $csv->insertOne($header);

        $csv->insertAll( $data );

        return $csv->toString();
    }

}