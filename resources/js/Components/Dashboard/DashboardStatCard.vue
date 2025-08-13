<template>
  <div 
    class="bg-white overflow-hidden shadow-sm rounded-lg p-6 cursor-pointer transition-all hover:shadow-md"
    :class="{ 'cursor-pointer': clickable }"
    @click="handleClick"
  >
    <div class="flex items-center">
      <div 
        class="p-3 rounded-full"
        :class="iconBgColor"
      >
        <component 
          :is="getIconComponent" 
          class="h-6 w-6"
          :class="iconTextColor"
          aria-hidden="true"
        />
      </div>
      <div class="ml-5 w-0 flex-1">
        <dl>
          <dt class="text-sm font-medium text-gray-500 truncate">
            {{ title }}
          </dt>
          <dd class="flex items-baseline">
            <div class="text-2xl font-semibold text-gray-900">
              {{ count }}
            </div>
            <div 
              v-if="percentage !== null" 
              :class="percentage >= 0 ? 'text-green-600' : 'text-red-600'"
              class="ml-2 flex items-baseline text-sm font-semibold"
            >
              <component 
                :is="percentage >= 0 ? 'ArrowUpIcon' : 'ArrowDownIcon'" 
                class="self-center flex-shrink-0 h-4 w-4"
                :class="percentage >= 0 ? 'text-green-500' : 'text-red-500'"
                aria-hidden="true"
              />
              <span class="sr-only">
                {{ percentage >= 0 ? 'Increased' : 'Decreased' }} by
              </span>
              {{ Math.abs(percentage) }}%
            </div>
          </dd>
        </dl>
      </div>
    </div>
    <div v-if="link || $slots.footer" class="mt-4">
      <slot name="footer">
        <a 
          v-if="link" 
          :href="link"
          class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
          @click.stop
        >
          {{ linkText }}
          <span v-if="!linkText" aria-hidden="true">
            &rarr;
          </span>
        </a>
      </slot>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
  ArrowUpIcon, 
  ArrowDownIcon,
  HeartIcon,
  EyeIcon,
  ShoppingBagIcon,
  CurrencyDollarIcon,
  UserGroupIcon,
  ClockIcon,
  StarIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationCircleIcon as ExclamationIcon,
  InformationCircleIcon
} from '@heroicons/vue/outline';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  count: {
    type: [Number, String],
    required: true
  },
  icon: {
    type: String,
    default: 'info'
  },
  iconColor: {
    type: String,
    default: 'indigo',
    validator: (value) => ['indigo', 'blue', 'green', 'red', 'yellow', 'purple', 'pink'].includes(value)
  },
  percentage: {
    type: Number,
    default: null
  },
  link: {
    type: String,
    default: ''
  },
  linkText: {
    type: String,
    default: 'View all'
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['view-all']);

const iconComponents = {
  heart: HeartIcon,
  eye: EyeIcon,
  bag: ShoppingBagIcon,
  dollar: CurrencyDollarIcon,
  users: UserGroupIcon,
  clock: ClockIcon,
  star: StarIcon,
  check: CheckCircleIcon,
  x: XCircleIcon,
  warning: ExclamationIcon,
  info: InformationCircleIcon
};

const getIconComponent = computed(() => {
  return iconComponents[props.icon] || InformationCircleIcon;
});

const iconBgColor = computed(() => {
  const colors = {
    indigo: 'bg-indigo-100',
    blue: 'bg-blue-100',
    green: 'bg-green-100',
    red: 'bg-red-100',
    yellow: 'bg-yellow-100',
    purple: 'bg-purple-100',
    pink: 'bg-pink-100'
  };
  return colors[props.iconColor] || 'bg-gray-100';
});

const iconTextColor = computed(() => {
  const colors = {
    indigo: 'text-indigo-600',
    blue: 'text-blue-600',
    green: 'text-green-600',
    red: 'text-red-600',
    yellow: 'text-yellow-600',
    purple: 'text-purple-600',
    pink: 'text-pink-600'
  };
  return colors[props.iconColor] || 'text-gray-600';
});

const clickable = computed(() => {
  return props.link || !!Object.keys(emit).length;
});

const handleClick = () => {
  if (props.link) {
    window.location.href = props.link;
  } else {
    emit('view-all');
  }
};
</script>
