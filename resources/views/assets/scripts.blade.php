<script>
  const mediable = {
    
    /**
     * Inserts a string at the cursor position in a given input element.
     *
     * @param {HTMLElement} inputEl - The input element.
     * @param {string} inputString - The string to insert.
     */
    insertAtCursor: (inputEl, inputString) => {
      let text = inputString.replaceAll('\\n', String.fromCharCode(13, 10));
      let cursorPosition = inputEl.selectionStart;
      let start = (inputEl.value).substring(0, cursorPosition);
      let end = (inputEl.value).substring(cursorPosition, inputEl.value.length);
      inputEl.value = start + text + end;
      cursorPosition = cursorPosition + text.length;
      inputEl.selectionStart = cursorPosition;
      inputEl.selectionEnd = cursorPosition;
      inputEl.focus();
    },

    /**
     * Formats a number of bytes into a string with a unit. The unit is chosen
     * based on the size of the number.
     *
     * @param {number} bytes - The number of bytes.
     * @param {number} [decimals=2] - The number of decimal places to include in the output.
     * @returns {string} The formatted string.
     */
    formatBytes: (bytes, decimals = 2) => {
      if (bytes === 0) return '0 Bytes';

      const k = 1024;
      const dm = decimals < 0 ? 0 : decimals;
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

      const i = Math.floor(Math.log(bytes) / Math.log(k));

      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
  };
</script>