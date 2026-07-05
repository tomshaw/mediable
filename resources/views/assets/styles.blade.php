<style>
    [x-cloak] {
        display: none !important;
    }

    .mediable :is(input, select, textarea):focus {
        outline: none !important;
        box-shadow: none !important;
    }

    .mediable :is(input, select, textarea, button):focus-visible {
        outline: 2px solid rgb(99 102 241 / 0.6) !important;
        outline-offset: 1px;
    }

    .mediable .scrollY {
        scrollbar-width: thin;
        /* For Firefox */
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
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
        background-color: rgba(155, 155, 155, 0.5);
        /* For Chrome, Safari, and Opera */
        border-radius: 20px;
        border: transparent;
    }

    .mediable .scrollX {
        scrollbar-width: thin;
        /* For Firefox */
        scrollbar-color: rgba(155, 155, 155, 0.5) transparent;
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
        background-color: rgba(155, 155, 155, 0.5);
        /* For Chrome, Safari, and Opera */
        border-radius: 20px;
        border: transparent;
    }

    /* Checkerboard uses a translucent mid-gray so it reads correctly on both the
       light (zinc-100) and dark (zinc-950) canvas set by utility classes. */
    .mediable .bg-pattern {
        background-image: linear-gradient(45deg, rgb(127 127 127 / 0.15) 25%, transparent 25%),
            linear-gradient(-45deg, rgb(127 127 127 / 0.15) 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, rgb(127 127 127 / 0.15) 75%),
            linear-gradient(-45deg, transparent 75%, rgb(127 127 127 / 0.15) 75%);
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
        border: 3px solid;
        border-color: currentColor currentColor currentColor transparent;
        border-radius: 9999px;
        width: 1rem;
        height: 1rem;
        display: inline-block;
        margin-left: 0.25rem;
        margin-right: 0.25rem;
        animation: spin 1s linear infinite;
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

    @media (prefers-reduced-motion: reduce) {

        .mediable *,
        .mediable *::before,
        .mediable *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }

        .attachment .attachment__item {
            opacity: 1;
        }
    }
</style>
