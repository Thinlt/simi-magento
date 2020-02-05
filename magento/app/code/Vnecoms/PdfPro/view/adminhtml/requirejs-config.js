
var config = {
    map: {
        "*": {
            "pdfWidget":"Vnecoms_PdfPro/js/widget",
            "easypdf_plugin":"Vnecoms_PdfPro/js/editor_plugin",
            "pdfTinyMCE":"Vnecoms_PdfPro/js/tinymce",
            "vnecoms.Popular":"Vnecoms_PdfPro/js/popular",
            "vnecomsEasypdf": "Vnecoms_PdfPro/js/easypdf",
            "owlCarousel": "Vnecoms_PdfPro/jquery-owlcarousel/owl.carousel.min",
            "pdfVariables": "Vnecoms_PdfPro/variables"
        }
    },
    "shims": {
        "jquery/filer": ["jquery"],
        "jquery/owlcarousel":["jquery"],
        "owlCarousel":["jquery"]
    },
    "paths": {
        "jquery/filer": "Vnecoms_PdfPro/jquery-filer/js/jquery-filer",
        "jquery/owlcarousel": "Vnecoms_PdfPro/jquery-owlcarousel/owl.carousel.min"
    },
    "deps": [
        "vnecoms.Popular"
    ]

};

