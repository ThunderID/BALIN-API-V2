<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    "accepted"             => ":attribute harus diterima.",
    "active_url"           => ":attribute Bukan URL yang valid.",
    "after"                => ":attribute setelah tanggal :date.",
    "alpha"                => ":attribute hanya boleh berupa huruf.",
    "alpha_dash"           => ":attribute hanya boleh berupa huruf, angka, dan garis.",
    "alpha_num"            => ":attribute hanya boleh berupa huruf dan angka.",
    "array"                => ":attribute harus berupa array.",
    "before"               => ":attribute sebelum tanggal :date.",
    "between"              => [
        "numeric" => ":attribute diantara :min dan :max.",
        "file"    => ":attribute diantara :min dan :max kilobytes.",
        "string"  => ":attribute diantara :min dan :max karakter.",
        "array"   => ":attribute harus bernilai antara :min dan :max item.",
    ],
    "boolean"              => ":attribute harus merupakan benar atau salah.",
    "confirmed"            => "Konfirmasi :attribute tidak cocok.",
    "date"                 => ":attribute bukan tanggal yang valid.",
    "date_format"          => ":attribute tidak cocok dengan format :format.",
    "different"            => ":attribute dan :other harus berbeda.",
    "digits"               => ":attribute harus :digits digit.",
    "digits_between"       => ":attribute harus diantara :min dan :max digit.",
    "email"                => ":attribute bukan alamat email yang sah.",
    "filled"               => ":attribute tidak boleh kosong.",
    "exists"               => ":attribute yang dipilih tidak sah.",
    "image"                => ":attribute harus berupa gambar.",
    "in"                   => ":attribute tidak sah.",
    "integer"              => ":attribute harus bilangan bulat.",
    "ip"                   => ":attribute harus merupakan alamat IP yang sah.",
    "max"                  => [
        "numeric" => ":attribute tidak boleh lebih dari :max.",
        "file"    => ":attribute tidak boleh lebih dari :max kilobytes.",
        "string"  => ":attribute tidak boleh lebih dari :max karakter.",
        "array"   => ":attribute tidak boleh lebih dari :max item.",
    ],
    "mimes"                => ":attribute harus file dengan tipe: :values.",
    "min"                  => [
        "numeric" => ":attribute paling tidak :min.",
        "file"    => ":attribute paling tidak :min kilobytes.",
        "string"  => ":attribute paling tidak :min karakter.",
        "array"   => ":attribute paling tidak least :min item.",
    ],
    "not_in"               => ":attribute tidak sah.",
    "numeric"              => ":attribute harus berupa angka.",
    "regex"                => ":attribute format tidak sah.",
    "required"             => ":attribute tidak boleh kosong.",
    "required_if"          => ":attribute tidak boleh kosong ketika :other :value.",
    "required_with"        => ":attribute tidak boleh kosong ketika :values ada.",
    "required_with_all"    => ":attribute tidak boleh kosong ketika :values ada.",
    "required_without"     => ":attribute tidak boleh kosong ketika :values tidak ada.",
    "required_without_all" => ":attribute tidak boleh kosong ketika nilai :values ada.",
    "same"                 => ":attribute dan :other harus sama.",
    "size"                 => [
        "numeric" => ":attribute harus :size.",
        "file"    => ":attribute harus :size kilobytes.",
        "string"  => ":attribute harus :size karakter.",
        "array"   => ":attribute harus memuat :size item.",
    ],
    "unique"               => ":attribute sudah dipakai.",
    "url"                  => ":attribute format tidak sah.",
    "timezone"             => ":attribute harus berupa zona yang sah.",

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
