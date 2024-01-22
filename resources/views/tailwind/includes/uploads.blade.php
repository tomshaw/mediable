<div @class(['flex items-center justify-center p-0 m-0 w-full', sizeof($files) ? 'h-auto' : 'h-full' ]) x-data="handleUploads()">

  @if (sizeof($files))

  <div class="overflow-hidden w-full">

    <table class="min-w-full text-center text-sm font-light">
      <tbody>
        @foreach($files as $index => $file)
        @if(!is_null($file))
        <tr class="border-b border-dashed">

          <td class="whitespace-nowrap px-4 py-2 text-left w-[8px] max-w-[8px]">
            <span class="text-gray-700">
              {{ $index+1 }}
            </span>
          </td>

          <td class="whitespace-nowrap px-4 py-2 text-left">
            <span class="text-gray-700 font-normal">
              {!! \Illuminate\Support\Str::limit($file->getClientOriginalName(), 40, '...') !!}
            </span>
          </td>

          <td class="whitespace-nowrap px-4 py-2 text-left">
            <span class="text-gray-700">
              {{ $file->getMimeType() }}
            </span>
          </td>

          <td class="whitespace-nowrap px-4 py-2 text-left">
            <span class="text-gray-700">
              ({{round($file->getSize() / 1024, 2)}}) KB
            </span>
          </td>

          <td class="whitespace-nowrap pe-6 py-2 text-right">
            <div>
              <button type="button" class="btn" wire:click="clearFile({{$index}})">Remove</button>
            </div>
          </td>

        </tr>
        @endif
        @endforeach
      </tbody>
    </table>

  </div>

  @else

  <form>
    <div class="h-auto py-6 px-7 text-center cursor-pointer border border-blue-500 border-dashed bg-blue-50 rounded-lg" @dragover.prevent @dragenter="dragEnter" @dragleave="dragLeave" @drop="drop" x-bind:class="{ 'border-2 border-red-500': enter }" x-on:click.prevent="fileClick($event)">
      <div class="m-0 flex items-center text-left">
        <span class="text-blue-500 leading-none" wire:loading.hidden wire.target="files">
          <img src="{{ asset("vendor/mediable/images/upload.png") }}" class="w-full h-full object-cover" alt="Upload files" />
        </span>
        <div class="ml-4">
          <h3 class="text-gray-900 font-bold text-lg mb-1">Drop files here or click to upload.</h3>
          <span class="text-gray-400 font-semibold text-sm">Upload up to {{ $this->getMaxFileUploads() }} files maximum {{ $this->getMaxUploadSize() }} MB.</span>
        </div>
      </div>
    </div>
    <div class="hidden">
      <input type="file" wire:model="files" multiple id="fileInput">
    </div>
  </form>

  <script>
    function handleUploads() {
      return {
        enter: false,
        leave: false,
        dropingFile: false,
        isUploading: false,
        progress: 0,
        error: null,
        dragEnter(e) {
          this.enter = true;
        },
        dragLeave(e) {
          this.leave = false;
        },
        drop(e) {
          e.preventDefault();
          let files = e.dataTransfer.files;
          if (files.length > 0) {
            this.transferFiles(files)
          }
          this.dropingFile = false;
        },
        fileClick(event) {
          document.getElementById('fileInput').click();
        },
        transferFiles(files) {
          this.$wire.uploadMultiple('files', files,
            (uploadedFilename) => {
              this.isUploading = false;
              this.progress = 0;
            },
            (error) => {
              this.error = error;
              this.isUploading = false;
              this.progress = 0;
            },
            (event) => {
              if (event.detail.progress) {
                this.progress = event.detail.progress;
              }
            }
          );
        }
      };
    }
  </script>

  @endif

</div>