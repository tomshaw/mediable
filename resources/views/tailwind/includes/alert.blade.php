<div :class="{
    'bg-red-200': alert.type === 'error',
    'bg-yellow-200': alert.type === 'warning',
    'bg-green-200': alert.type === 'success',
    'bg-blue-200': alert.type === 'info',
    'absolute flex items-center justify-between p-0 m-0 px-4 w-full h-[50px] min-h-[50px] will-change-transform': true
}" x-data="initModalAlert()" x-show="alert.show" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90">
  <div class="flex items-center justify-start gap-2">
    <span class="text-sm text-gray-700">{{$alert->message}}</span>
  </div>
  <div class="flex items-center justify-end gap-2">
    <button type="button" class="relative flex items-center justify-center px-4 py-1.5 gap-x-2 bg-[#555] text-white rounded-full text-xs font-normal cursor-pointer transition-all duration-100 ease-in" wire:click="closeAlert()">Dismiss</button>
  </div>
</div>
@script
<script>
  Alpine.data('initModalAlert', () => ({
    alert: @entangle('alert').live,
    timer: null,
    startTimer() {
      if (this.alert.show) {
        this.timer = setTimeout(() => {
          this.alert.show = false;
        }, 3000);
      }
    },
    clearTimer() {
      clearTimeout(this.timer);
    },
    init() {
      this.$watch('alert.show', value => {
        if (value) {
          this.startTimer();
        } else {
          this.clearTimer();
        }
      });
    },
  }))
</script>
@endscript