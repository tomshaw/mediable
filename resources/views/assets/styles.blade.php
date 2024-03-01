<style>
    .mediable input:focus,
    .mediable select:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    .mediable .control-input {
        display: block;
        color: #696969;
        border: 1px solid #dfdfdf;
        width: 100%;
        padding: 0.4rem 0.75rem;
        appearance: none;
        border-radius: .5rem;
        font-size: .75rem;
        font-weight: 500;
        line-height: 1.25rem;
    }

    .mediable .control-select {
        display: block;
        cursor: pointer;
        border-radius: 9999px;
        border-width: 2px;
        border-color: transparent;
        background-color: #555;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
        font-size: .75rem;
        line-height: 1rem;
        color: #fff;
    }

    .mediable .btn {
        display: inline-block;
        vertical-align: middle;
        color: #4e4e4e;
        text-align: center;
        font-size: 11px;
        font-weight: 500;
        line-height: 1;
        padding: 6px 9px;
        user-select: none;
        margin: 0 2px;
        border-radius: 4px;
        border: 1px solid #E3E3E3;
        cursor: pointer;
        box-shadow: inset 0 1px #fff, 0 1px 2px #666;
        text-shadow: 0 1px 1px #FFF;
        background-color: #e6e6e6;
        background-image: -webkit-linear-gradient(top, #F3F3F3, #D7D7D7);
        white-space: nowrap;
    }

    .mediable .btn:hover,
    .mediable .btn:focus {
        color: #686868;
    }

    .mediable table {
        min-width: 100%;
        text-align: center;
        font-size: 0.875rem;
        font-weight: 300;
        padding: 0 !important;
        border-collapse: collapse;
    }

    .mediable table>thead>tr>th,
    .mediable table>tbody>tr>td {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: middle;
    }

    .mediable table>tbody>tr {
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        cursor: pointer;
    }

    .mediable table>tbody>tr:first-of-type {
        border-top: 1px solid transparent;
    }

    .mediable table>tbody>tr:last-of-type {
        border-bottom: 1px solid transparent;
    }

    .mediable table>tbody>tr.selected {
        background-color: #f2f2f2;
    }

    .attachment .attachment-item {
        animation: fadeIn 200ms forwards;
        animation-delay: 600ms;
        opacity: 0;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    .audio-animation {
        animation: audio-animation 1.2s ease infinite alternate;
    }

    @keyframes audio-animation {
        10% {
            height: 30%;
        }

        30% {
            height: 100%;
        }

        60% {
            height: 50%;
        }

        80% {
            height: 75%;
        }

        100% {
            height: 60%;
        }
    }
</style>