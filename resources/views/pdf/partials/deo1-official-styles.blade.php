<style>
    /* A4 portret — margine kao na zvaničnom obrascu */
    @page {
        margin: 14mm 16mm 16mm 16mm;
        size: A4 portrait;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 8px;
        color: #000;
        background: #fff;
        margin: 0;
        padding: 0;
    }

    /* Sadržaj centriran, ne razvlači se do ivica ekrana */
    .deo1-document {
        width: 178mm;
        max-width: 100%;
        margin: 0 auto;
    }

    table.deo1-table {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
        background: #fff;
        page-break-inside: avoid;
    }

    table.deo1-table th,
    table.deo1-table td {
        border: 1px solid #222;
        padding: 3px 4px;
        text-align: center;
        vertical-align: middle;
        line-height: 1.2;
    }

    /* Naslov */
    .header {
        background: #e6e6e6;
        font-weight: bold;
    }

    .header-title {
        font-size: 9px;
        text-transform: uppercase;
        text-align: center;
        padding: 5px 4px !important;
    }

    .header-ref {
        font-size: 7px;
        text-align: right;
        text-transform: uppercase;
        padding: 3px 5px !important;
        line-height: 1.25;
    }

    /* Meta polja */
    .meta-label {
        background: #e6e6e6;
        font-weight: bold;
        text-align: left;
        padding-left: 5px !important;
    }

    .meta-value {
        background: #fff;
        font-weight: bold;
        text-align: center;
    }

    .meta-value-wide {
        background: #fff;
        font-weight: bold;
        text-align: left;
        padding-left: 6px !important;
    }

    /* Sekcije tabele */
    .section {
        background: #d0d0d0;
        font-weight: bold;
        font-size: 7px;
        text-transform: uppercase;
        padding: 4px 2px !important;
    }

    .subsection {
        background: #f2f2f2;
        font-weight: bold;
        font-size: 6px;
        padding: 2px !important;
    }

    .subsection-vertical {
        background: #f2f2f2;
        font-weight: bold;
        font-size: 5px;
        padding: 1px !important;
        height: 48px;
        line-height: 1.05;
    }

    /* Redovi podataka — kompaktni da stane 31 red na A4 */
    .data-row td {
        height: 10px;
        font-size: 6.5px;
        padding: 1px 2px !important;
        background: #fff;
    }

    .cell-left { text-align: left !important; }
    .cell-right { text-align: right !important; }

    /* UKUPNO */
    .ukupno-label {
        background: #e6e6e6;
        font-weight: bold;
        text-align: left !important;
        padding-left: 5px !important;
    }

    .ukupno-value {
        background: #fff;
        font-weight: bold;
    }

    .ukupno-block {
        background: #e6e6e6;
    }

    .footnotes {
        margin-top: 4px;
        font-size: 5.5px;
        line-height: 1.35;
        width: 100%;
    }

    .doc-meta-footer {
        margin-top: 3px;
        font-size: 5px;
        color: #555;
        width: 100%;
    }

    .page-break { page-break-before: always; }
</style>
