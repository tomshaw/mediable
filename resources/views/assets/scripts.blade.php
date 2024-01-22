<script>
  const mediable = () => {

    const insertAtCursor = (inputEl, inputString) => {
      let text = inputString.replaceAll('\\n', String.fromCharCode(13, 10));
      let cursorPosition = inputEl.selectionStart;
      let start = (inputEl.value).substring(0, cursorPosition);
      let end = (inputEl.value).substring(cursorPosition, inputEl.value.length);
      inputEl.value = start + text + end;
      cursorPosition = cursorPosition + text.length;
      inputEl.selectionStart = cursorPosition;
      inputEl.selectionEnd = cursorPosition;
      inputEl.focus();
    };

    return {
      show: false,
      inputId: '',
      init() {
        window.addEventListener('mediable:alert', event => this.alert(event?.detail));
        window.addEventListener('mediable:insert', event => this.insert(event?.detail?.selected));
        window.addEventListener('mediable:open', event => this.open(event?.detail?.id));
        window.addEventListener('mediable:close', event => this.close());
        window.addEventListener('audio:start', event => this.audioStart(event?.detail?.id));
        window.addEventListener('audio:pause', event => this.audioPause(event?.detail?.id));
      },
      audioStart(id) {
        const audio = document.getElementById('audioPlayer' + id);

        if (audio) {
          audio.play();
        }
      },
      audioPause(id) {
        const audio = document.getElementById('audioPlayer' + id);

        if (audio) {
          audio.pause();
        }
      },
      open(inputId) {
        if (inputId) {
          this.inputId = inputId;
          Livewire.dispatch('modal:type', {
            modalType: 'attachment'
          });
        } else {
          this.inputId = '';
          Livewire.dispatch('modal:typel', {
            modalType: 'default'
          });
        }
      },
      close() {
        this.show = false;
      },
      insert(selected) {
        let insert = [];

        for (let i = 0; i < selected.length; i++) {
          let mediaType = selected[i].file_type.toLowerCase();

          let fileTitle = (selected[i].file_title) ? selected[i].file_title : selected[i].file_name;

          if (mediaType.indexOf('image') != -1) {
            insert.push('<a href="' + selected[i].file_url + '"><img src="' + selected[i].file_url + '" alt="' + fileTitle + '"></a>\n');
          } else if (mediaType.indexOf('audio') != -1) {
            insert.push('<audio controls autoplay><source src="' + selected[i].file_url + '" type="' + selected[i].file_type + '"></audio>\n');
          } else if (mediaType.indexOf('video') != -1) {
            insert.push('<video controls autoplay><source src="' + selected[i].file_url + '" type="' + selected[i].file_type + '"></video>\n');
          } else {
            insert.push('<a href="' + selected[i].file_url + '">' + fileTitle + '</a>');
          }
        }
        if (insert.length) {
          const el = document.getElementById(this.inputId);
          if (el) {
            insertAtCursor(el, insert.join(' '));
          }
        }
      },
      alert(options) {
        console.warn('alert', options);
      }
    }
  };
</script>