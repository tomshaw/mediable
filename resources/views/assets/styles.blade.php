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