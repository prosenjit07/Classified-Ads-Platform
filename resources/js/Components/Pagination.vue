<template>
    <div v-if="hasPages" class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
        <!-- Mobile view -->
        <div class="flex justify-between flex-1 sm:hidden">
            <button 
                @click="previousPage"
                :disabled="pagination.current_page <= 1"
                :class="{
                    'opacity-50 cursor-not-allowed': pagination.current_page <= 1,
                    'text-gray-500 hover:text-gray-700': pagination.current_page > 1,
                }"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
                Previous
            </button>
            <button 
                @click="nextPage"
                :disabled="pagination.current_page >= pagination.last_page"
                :class="{
                    'opacity-50 cursor-not-allowed': pagination.current_page >= pagination.last_page,
                    'text-gray-500 hover:text-gray-700': pagination.current_page < pagination.last_page,
                }"
                class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
                Next
            </button>
        </div>

        <!-- Desktop view -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ pagination.from || 0 }}</span>
                    to
                    <span class="font-medium">{{ pagination.to || 0 }}</span>
                    of
                    <span class="font-medium">{{ pagination.total || 0 }}</span>
                    results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <!-- Previous Page Link -->
                    <button 
                        @click="previousPage"
                        :disabled="pagination.current_page <= 1"
                        :class="{
                            'opacity-50 cursor-not-allowed': pagination.current_page <= 1,
                            'text-gray-500 hover:bg-gray-50': pagination.current_page > 1,
                        }"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50"
                    >
                        <span class="sr-only">Previous</span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Page Numbers -->
                    <template v-for="(link, index) in pageRange" :key="index">
                        <span 
                            v-if="link === '...'"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300"
                        >
                            ...
                        </span>
                        <button
                            v-else
                            @click="changePage(link)"
                            :class="{
                                'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link === pagination.current_page,
                                'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': link !== pagination.current_page,
                            }"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium border"
                            :aria-current="link === pagination.current_page ? 'page' : undefined"
                        >
                            {{ link }}
                        </button>
                    </template>

                    <!-- Next Page Link -->
                    <button 
                        @click="nextPage"
                        :disabled="pagination.current_page >= pagination.last_page"
                        :class="{
                            'opacity-50 cursor-not-allowed': pagination.current_page >= pagination.last_page,
                            'text-gray-500 hover:bg-gray-50': pagination.current_page < pagination.last_page,
                        }"
                        class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50"
                    >
                        <span class="sr-only">Next</span>
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
        default: () => ({
            current_page: 1,
            from: null,
            last_page: 1,
            links: [],
            path: '',
            per_page: 10,
            to: null,
            total: 0,
        }),
    },
    only: {
        type: Array,
        default: () => [],
    },
    preserveScroll: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['page-change']);

const hasPages = computed(() => {
    return props.pagination.last_page > 1;
});

const pageRange = computed(() => {
    const current = props.pagination.current_page;
    const last = props.pagination.last_page;
    const delta = 1;
    const left = current - delta;
    const right = current + delta + 1;
    const range = [];
    const rangeWithDots = [];
    let l;

    for (let i = 1; i <= last; i++) {
        if (i === 1 || i === last || (i >= left && i < right)) {
            range.push(i);
        } else if (i < left && i === left - 1) {
            range.push('...');
        } else if (i > right && i === right + 1) {
            range.push('...');
        }
    }

    range.forEach((i) => {
        if (l) {
            if (i - l === 2) {
                rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
                rangeWithDots.push('...');
            }
        }
        rangeWithDots.push(i);
        l = i;
    });

    return rangeWithDots;
});

const changePage = (page) => {
    if (page === '...' || page === props.pagination.current_page) {
        return;
    }
    
    const url = new URL(window.location.href);
    url.searchParams.set('page', page);
    
    router.visit(url.toString(), {
        only: props.only.length ? props.only : undefined,
        preserveScroll: props.preserveScroll,
        onSuccess: () => {
            window.scrollTo(0, 0);
            emit('page-change', page);
        },
    });
};

const previousPage = () => {
    if (props.pagination.current_page > 1) {
        changePage(props.pagination.current_page - 1);
    }
};

const nextPage = () => {
    if (props.pagination.current_page < props.pagination.last_page) {
        changePage(props.pagination.current_page + 1);
    }
};
</script>
