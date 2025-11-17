<template>
  <teleport to="body">
    <transition name="modal">
      <div v-if="modelValue" class="fixed inset-0 z-50 overflow-y-auto" @click="close">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <!-- Backdrop -->
          <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

          <!-- Center modal -->
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

          <!-- Modal panel -->
          <div
            class="inline-block w-full overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg"
            @click.stop
          >
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="w-full mt-3 text-center sm:mt-0 sm:text-left">
                  <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                    {{ title }}
                  </h3>
                  <div class="mt-4">
                    <slot></slot>
                  </div>
                </div>
              </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                v-if="!hideConfirm"
                type="button"
                :disabled="confirmDisabled"
                @click="$emit('confirm')"
                class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ confirmText }}
              </button>
              <button
                type="button"
                @click="close"
                class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm"
              >
                {{ cancelText }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { watch } from 'vue';

const props = defineProps({
  modelValue: Boolean,
  title: String,
  confirmText: {
    type: String,
    default: 'Confirm',
  },
  cancelText: {
    type: String,
    default: 'Cancel',
  },
  confirmDisabled: {
    type: Boolean,
    default: false,
  },
  hideConfirm: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['update:modelValue', 'confirm', 'close']);

function close() {
  emit('update:modelValue', false);
  emit('close');
}

// Prevent body scroll when modal is open
watch(() => props.modelValue, (value) => {
  if (value) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
});
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
