<div :class="{
    'bg-red-50 dark:bg-red-950 border-red-200 dark:border-red-900': alert.type === 'error',
    'bg-amber-50 dark:bg-amber-950 border-amber-200 dark:border-amber-900': alert.type === 'warning',
    'bg-emerald-50 dark:bg-emerald-950 border-emerald-200 dark:border-emerald-900': alert.type === 'success',
    'bg-sky-50 dark:bg-sky-950 border-sky-200 dark:border-sky-900': alert.type === 'info',
    'absolute flex items-center justify-between p-0 m-0 px-4 w-full h-12 min-h-12 max-h-12 border-b will-change-transform z-50': true
}" x-data="initModalAlert()" x-show="alert.show" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90">
  <div class="flex items-center justify-start gap-2.5 min-w-0">
    <span class="shrink-0 inline-flex w-2 h-2 rounded-full" :class="{
        'bg-red-500': alert.type === 'error',
        'bg-amber-500': alert.type === 'warning',
        'bg-emerald-500': alert.type === 'success',
        'bg-sky-500': alert.type === 'info'
    }"></span>
    <span class="text-xs font-medium truncate" :class="{
        'text-red-800 dark:text-red-200': alert.type === 'error',
        'text-amber-800 dark:text-amber-200': alert.type === 'warning',
        'text-emerald-800 dark:text-emerald-200': alert.type === 'success',
        'text-sky-800 dark:text-sky-200': alert.type === 'info'
    }">{{ $alert->message }}</span>
  </div>
  <div class="flex items-center justify-end gap-2 shrink-0">
    <button type="button" class="inline-flex items-center h-7 rounded-md px-2.5 text-xs font-medium text-zinc-600 hover:bg-zinc-950/5 dark:text-zinc-300 dark:hover:bg-white/10 cursor-pointer transition-colors" wire:click="closeAlert">Dismiss</button>
  </div>
</div>
