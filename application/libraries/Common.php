<?php
defined('BASEPATH') or exit('No direct script access allowed');

// ! Password incryption - FurRs

class Common
{
    public $num = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

    public $requirements_checklist = [
        "Government Numbers" => [
            "TIN",
            "SSS Number",
            "PhilHealth Number",
            "Pag-IBIG Number"
        ],
        "Personal Documents" => [
            "2 pcs. 1x1 Picture",
            "PSA Birth Certificate (1 copy)",
            "Cedula",
            "2 Valid IDs (2 copies each)"
        ],
        "Health Requirements" => [
            "Urinalysis",
            "X-ray Film and Result",
            "CBC Test Results"
        ],
        "Employment Documents (if applicable)" => [
            "Certificate of Employment",
            "Form 2316 / Income Tax Return"
        ],
        "Education Document" => [
            "Transcript of Records (TOR)"
        ]
    ];
}
