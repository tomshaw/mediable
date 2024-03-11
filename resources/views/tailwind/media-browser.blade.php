<div class="mediable fixed inset-0 h-full w-full z-50 will-change-transform" x-data="initMediableBrowser()" x-show="state.show" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90">

    <div class="absolute inset-0 bg-black bg-opacity-40" @click="show = false"></div>

    <div @class(['bg-white flex justify-start fixed overflow-hidden', 'rounded-lg shadow-lg top-8 left-8 right-8 bottom-8'=> !$fullScreen, 'rounded-none shadow-none top-0 left-0 right-0 bottom-0' => $fullScreen])>
        <div class="flex h-full bg-white flex-grow overflow-hidden">
            <div class="relative flex flex-col justify-between w-full h-full overflow-hidden">

                <div class="bg-gray-100 h-12 min-h-12 max-h-12 xl:h-14 xl:min-h-14 xl:max-h-14 2xl:h-16 2xl:min-h-16 2xl:max-h-16 w-full">
                    @include("mediable::tailwind.includes.header")
                </div>

                <div class="bg-gray-200 h-full overflow-hidden flex justify-between border-t border-b border-gray-300 w-full">

                    @if($show->isShowMetaInfo() && !$panel->isUploadMode())
                    <div class="relative bg-gray-200 border-r border-gray-300 w-56 min-w-56 max-w-56 xl:w-60 xl:min-w-60 xl:max-w-60 2xl:w-64 2xl:min-w-64 2xl:max-w-64 h-full">
                        <div class="relative flex items-center justify-center h-full overflow-hidden">
                            @if(!$panel->isEditorMode())
                            @include("mediable::tailwind.includes.meta")
                            @elseif($panel->isEditorMode())
                            @include("mediable::tailwind.includes.form")
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="bg-gray-200 w-full h-full overflow-y-auto">
                        <div class="flex flex-col h-full">

                            <div class="relative flex items-center justify-center h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
                                @include("mediable::tailwind.includes.toolbar")
                                @include("mediable::tailwind.includes.alert")
                            </div>

                            <div class="flex items-center justify-center flex-grow overflow-auto border-t border-b border-gray-300">
                                <div class="w-full h-full overflow-y-auto">
                                    <div class="relative p-0 m-0 h-full w-full">
                                        <div @class(["absolute top-0 left-0 bottom-0 right-0 h-full w-full p-0 m-0 overflow-auto scrollY opacity-0 invisible transition-opacity duration-300 delay-200", "opacity-100 !visible z-10"=> $panel->isThumbMode()])>
                                            @include("mediable::tailwind.includes.attachments")
                                        </div>
                                        <div @class(["absolute top-0 left-0 bottom-0 right-0 h-full w-full p-0 m-0 overflow-auto scrollY opacity-0 invisible transition-opacity duration-300 delay-200", "opacity-100 !visible z-10"=> $panel->isPreviewMode()])>
                                            @include("mediable::tailwind.includes.previews")
                                        </div>
                                        <div @class(["absolute top-0 left-0 bottom-0 right-0 h-full w-full p-0 m-0 overflow-auto opacity-0 invisible transition-opacity duration-300 delay-200", "opacity-100 !visible z-10"=> $panel->isEditorMode()])>
                                            @include("mediable::tailwind.includes.editor")
                                        </div>
                                        <div @class(["absolute top-0 left-0 bottom-0 right-0 h-full w-full p-0 m-0 overflow-auto opacity-0 invisible transition-opacity duration-300 delay-200", "opacity-100 !visible z-10"=> $panel->isUploadMode()])>
                                            @include("mediable::tailwind.includes.uploads")
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if(!$panel->isUploadMode() && !$panel->isEditorMode())
                            <div class="relative flex items-center justify-center h-10 min-h-10 max-h-10 xl:h-11 xl:min-h-11 xl:max-h-11 2xl:h-12 2xl:min-h-12 2xl:max-h-12 w-full">
                                <div class="flex items-center justify-between h-full w-full px-4">
                                    @if($show->isShowPagination() && method_exists($data, 'links'))
                                    {!! $data->links("mediable::tailwind.includes.pagination") !!}
                                    @endif
                                    @if($show->isShowPerPage() && method_exists($data, 'links') && $data->hasPages())
                                    <div class="hidden xl:block">
                                        <select class="control-select" wire:model.live="perPage">
                                            @foreach($perPageValues as $value)
                                            <option value="{{$value}}"> @if($value == 0) All @else {{ $value }} @endif</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                        </div>
                    </div>

                    @if($show->isShowSidebar() && !$panel->isUploadMode())
                    <div class="relative bg-gray-200 border-l border-gray-300 w-56 min-w-56 max-w-56 xl:w-60 xl:min-w-60 xl:max-w-60 2xl:w-64 2xl:min-w-64 2xl:max-w-64 h-full">
                        @include("mediable::tailwind.includes.sidebar")
                    </div>
                    @endif

                </div>

                @if($show->isShowImageStrip() && !$panel->isUploadMode() && !$panel->isEditorMode())
                <div class="hidden 2xl:block bg-[#e5e7eb] border-b border-gray-300 h-[100px] max-h-[100px] min-h-[100px] w-full overflow-hidden">
                    <div class="flex items-center justify-start h-full w-full px-4 overflow-x-auto scrollX">
                        @include("mediable::tailwind.includes.strip")
                    </div>
                </div>
                @endif

                @if(!$panel->isUploadMode() && !$panel->isEditorMode() && count($this->selected))
                <div class="bg-[#e6e6e6] h-[60px] max-h-[60px] min-h-[60px] w-full">
                    @include("mediable::tailwind.includes.footer")
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('initMediableBrowser', () => ({

        state: @entangle('modal').live,

        init() {
            Livewire.on('mediable.insert', event => this.insert(event?.selected));
            Livewire.on('mediable.scroll', event => this.scrollTo(event?.id));
            Livewire.on('mediable.confirm', event => this.confirm(event));
            Livewire.on('audio.start', event => this.audioStart(event?.id));
            Livewire.on('audio.pause', event => this.audioPause(event?.id));

            this.initTextCopy();
        },

        confirm(event) {
            if (window.confirm(event.message)) {
                this.$dispatch(event.type);
            }
        },

        scrollTo(id) {
            const item = document.getElementById('attachment-id-' + id);

            if (item) {
                item.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        },

        audioStart(id) {
            const audio = document.getElementById('audioPlayer' + id);
            const progressBar = document.getElementById('audioProgress' + id);

            if (audio) {
                audio.play();

                audio.addEventListener('timeupdate', () => {
                    if (audio.duration) {
                        const progress = (audio.currentTime / audio.duration) * 100;

                        progressBar.style.width = progress + '%';
                    }
                });

                audio.addEventListener('ended', () => {
                    this.$dispatch('audio.pause', {
                        id: id
                    });

                    progressBar.style.width = '0%';
                });
            }
        },

        audioPause(id) {
            const audio = document.getElementById('audioPlayer' + id);

            if (audio) {
                audio.pause();
            }
        },

        insert(selected) {
            if (!Array.isArray(selected)) {
                return;
            }

            let insert = selected.map(item => {
                let mediaType = item.file_type.toLowerCase();
                let fileTitle = item.file_title || item.file_name;

                if (mediaType.includes('image')) {
                    return `<a href="${item.file_url}"><img src="${item.file_url}" alt="${fileTitle}"></a>\n`;
                } else if (mediaType.includes('audio')) {
                    return `<audio controls autoplay><source src="${item.file_url}" type="${item.file_type}"></audio>\n`;
                } else if (mediaType.includes('video')) {
                    return `<video controls autoplay><source src="${item.file_url}" type="${item.file_type}"></video>\n`;
                } else {
                    return `<a href="${item.file_url}">${fileTitle}</a>`;
                }
            });

            if (insert.length) {
                const el = document.getElementById(this.state.elementId);
                if (el) {
                    Mediable.insertAtCursor(el, insert.join(' '));
                }
            }
        },

        initTextCopy() {
            document.querySelectorAll('[data-textcopy]').forEach(function(element) {
                element.addEventListener('click', function() {
                    var text = this.innerText;
                    var textarea = document.createElement('textarea');
                    textarea.value = text;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                });
            });
        }
    }))
</script>
@endscript