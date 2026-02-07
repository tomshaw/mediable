<style>
    .mediable input:focus,
    .mediable select:focus {
        outline: none !important;
        box-shadow: none !important;
    }

    .mediable .scrollY {
        scrollbar-width: thin;
        /* For Firefox */
        scrollbar-color: rgba(155, 155, 155, 0.7) transparent;
        /* For Firefox */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }

    .mediable .scrollY::-webkit-scrollbar {
        width: 7px;
        /* For Chrome, Safari, and Opera */
    }

    .mediable .scrollY::-webkit-scrollbar-track {
        background: transparent;
        /* For Chrome, Safari, and Opera */
    }

    .mediable .scrollY::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.7);
        /* For Chrome, Safari, and Opera */
        border-radius: 20px;
        border: transparent;
    }

    .mediable .scrollX {
        scrollbar-width: thin;
        /* For Firefox */
        scrollbar-color: rgba(155, 155, 155, 0.7) transparent;
        /* For Firefox */
        overflow-x: auto;
        /* Enable horizontal scrolling */
    }

    .mediable .scrollX::-webkit-scrollbar {
        height: 5px;
        /* For Chrome, Safari, and Opera */
    }

    .mediable .scrollX::-webkit-scrollbar-track {
        background: transparent;
        /* For Chrome, Safari, and Opera */
    }

    .mediable .scrollX::-webkit-scrollbar-thumb {
        background-color: rgba(155, 155, 155, 0.7);
        /* For Chrome, Safari, and Opera */
        border-radius: 20px;
        border: transparent;
    }

    .mediable .control-input {
        display: block;
        color: rgb(82 82 82);
        border: 1px solid #dfdfdf;
        width: 100%;
        padding: 0.4rem 0.75rem;
        appearance: none;
        border-radius: .5rem;
        font-size: .75rem;
        font-weight: 500;
        line-height: 1.25rem;
        letter-spacing: 0.02em;
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
        font-weight: 500;
        line-height: 1rem;
        letter-spacing: 0.02em;
        color: #fafafa;
    }

    .bg-pattern {
        background-color: #f3f4f6;
        background-image: linear-gradient(45deg, #cccccc 25%, transparent 25%),
            linear-gradient(-45deg, #cccccc 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, #cccccc 75%),
            linear-gradient(-45deg, transparent 75%, #cccccc 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    }

    .attachment .attachment__item {
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

    .spinner {
        border: 4px solid #d1d5db;
        border-left-color: #374151;
        border-radius: 9999px;
        width: 1.25rem;
        height: 1.25rem;
        display: inline-block;
        margin-left: 0.25rem;
        margin-right: 0.25rem;
        animation: spin 1s linear infinite;
    }

    @media (prefers-color-scheme: dark) {
        .spinner {
            border-color: #9ca3af;
            border-left-color: #f3f4f6;
        }
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
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