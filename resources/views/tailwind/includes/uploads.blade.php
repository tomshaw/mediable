<div @class(['flex items-center justify-center p-0 m-0 w-full', sizeof($files) ? 'h-auto' : 'h-full' ]) x-data="initMediableUploads()">

  @if (sizeof($files))
  <div class="flex flex-col items-center justify-center h-auto w-full max-w-screen-lg p-4 md:p-6 lg:p-8 m-0">
    <div class="w-full">
      @if(count($files) >= 1)
      <div class="flex justify-between gap-2 mb-2">
        <span class="text-sm text-gray-500">{{ count($files) }} total files selected.</span>
        <span class="text-sm text-gray-500">{{ $this->formatBytes($this->getTotalUploadSize()) }} total upload size.</span>
      </div>
      @endif
      <div class="overflow-x-auto md:overflow-visible">
        <table class="border-collapse table-fixed w-full text-sm shadow-md">
          <thead class="bg-[#E6E6E6]">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-[20px]">Id</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-[200px]">Name</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Size</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Status</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Progress</th>
              <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($files as $index => $file)
            <tr>
              <td class="px-6 py-2">{{ $index+1 }}</td>
              <td class="px-6 py-2 w-[200px]">{!! \Illuminate\Support\Str::limit($file->getClientOriginalName(), 40, '...') !!}</td>
              <td class="px-6 py-2">{{ $file->getMimeType() }}</td>
              <td class="px-6 py-2">{{$this->formatBytes($file->getSize())}}</td>
              <td class="px-6 py-2 hidden lg:table-cell">Pending</td>
              <td class="px-6 py-2 hidden lg:table-cell">0%</td>
              <td class="px-6 py-2 whitespace-nowrap flex items-center justify-center">
                <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="clearFile({{$index}})">Remove</button>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @else
  <form>
    <div class="h-auto max-w-[500px] py-6 px-7 text-center cursor-pointer border border-blue-500 border-dashed bg-blue-50 rounded-lg" @dragover.prevent @dragenter="dragEnter" @dragleave="dragLeave" @drop="drop" x-bind:class="{ 'border-red-500': enter }" x-on:click.prevent="fileClick($event)">
      <div class="m-0 p-2 flex items-center text-left">
        <span class="text-blue-500 leading-none">
          <img src="{{ asset("vendor/mediable/images/upload.png") }}" class="w-full h-full object-cover" alt="Upload files" />
        </span>
        <div class="ml-4">
          <h3 class="text-gray-800 font-bold text-lg mb-1">Drop files here or click to upload.</h3>
          <span class="text-gray-400 font-semibold text-sm">Upload up to <span x-text="maxFileUploads"></span> files not to exceed <span x-text="Mediable.formatBytes(maxUploadSize)"></span>. Maximum single file size is <span x-text="Mediable.formatBytes(maxUploadFileSize)"></span>.</span>
        </div>
      </div>
    </div>
    <div class="hidden">
      <input type="file" id="fileInput" wire:model="files" multiple>
    </div>
  </form>

  @endif

</div>

@script
<script>
  Alpine.data('initMediableUploads', () => ({
    error: null,
    enter: false,
    leave: false,
    progress: 0,
    dropingFile: false,

    maxFileUploads: 0,
    maxUploadFileSize: 0,
    maxUploadSize: 0,
    memoryLimit: 0,
    postMaxSize: 0,

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
      this.enter = false;
    },

    fileClick(event) {
      document.getElementById('fileInput').click();
    },

    transferFiles(files) {
      this.error = null;

      // Check total upload size.
      this.maxUploadSizeCheck(files);

      // Check individual upload file size.
      this.maxUploadFileSizeCheck(files);

      if (this.error) {
        console.warn('error', this.error);

        this.$wire.dispatch('mediable.alert', {
          type: 'error',
          message: this.error,
        });
        return;
      }

      this.$wire.uploadMultiple('files', files,
        (uploadedFilename) => {
          this.progress = 0;
        },
        (error) => {
          console.warn('uploadMultipleError', error);

          this.error = error;
          this.progress = 0;

          this.$wire.dispatch('mediable.alert', {
            type: 'error',
            message: error || 'An error occurred while uploading the file'
          });
        },
        (event) => {
          if (event.detail.progress) {
            this.progress = event.detail.progress;
          }
        }
      );
    },

    maxUploadSizeCheck(files) {
      let totalSize = 0;
      for (let i = 0; i < files.length; i++) {
        totalSize += files[i].size;
      }

      if (totalSize > this.maxUploadSize) {
        this.error = 'Maximum upload size of ' + mediable.formatBytes(this.maxUploadSize) + ' bytes has been exceeded.'
        return;
      }
    },

    maxUploadFileSizeCheck(files) {
      for (let i = 0; i < files.length; i++) {
        if (files[i].size > this.maxUploadFileSize) {
          this.error = 'File upload ' + files[i].name + ' exceeds maximum upload size of ' + mediable.formatBytes(this.maxUploadFileSize) + ' bytes.';
          return;
        }
      }
    },

    init() {
      window.addEventListener('server:limits', event => {
        this.maxUploadSize = event.detail.maxUploadSize;
        this.maxFileUploads = event.detail.maxFileUploads;
        this.maxUploadFileSize = event.detail.maxUploadFileSize;
        this.postMaxSize = event.detail.postMaxSize;
        this.memoryLimit = event.detail.memoryLimit;
      });

      let fileInput = document.querySelector('#fileInput');

      fileInput.addEventListener('change', () => {
        let files = fileInput.files;
        if (files.length > 0) {
          this.transferFiles(files);
        }
      });
    }
  }))
</script>
@endscript