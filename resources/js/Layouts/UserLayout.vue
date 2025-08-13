<template>
  <AppLayout>
    <!-- Sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
      <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4">
        <div class="flex h-16 shrink-0 items-center">
          <Link :href="route('dashboard')">
            <ApplicationLogo class="h-8 w-auto" />
          </Link>
        </div>
        <nav class="flex flex-1 flex-col">
          <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
              <ul role="list" class="-mx-2 space-y-1">
                <li v-for="item in navigation" :key="item.name">
                  <Link 
                    :href="item.href" 
                    :class="[
                      item.current
                        ? 'bg-gray-50 text-indigo-600'
                        : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600',
                      'group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6'
                    ]"
                  >
                    <component 
                      :is="item.icon" 
                      :class="[
                        item.current ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600',
                        'h-6 w-6 shrink-0'
                      ]" 
                      aria-hidden="true" 
                    />
                    {{ item.name }}
                  </Link>
                </li>
              </ul>
            </li>
            <li class="mt-auto">
              <Link 
                :href="route('profile.edit')" 
                class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-gray-50 hover:text-indigo-600"
              >
                <Cog6ToothIcon 
                  class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-indigo-600" 
                  aria-hidden="true" 
                />
                Settings
              </Link>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Mobile menu -->
    <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-white p-4 shadow-sm sm:px-6 lg:hidden">
      <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="mobileMenuOpen = true">
        <span class="sr-only">Open sidebar</span>
        <Bars3Icon class="h-6 w-6" aria-hidden="true" />
      </button>
      <div class="flex-1 text-sm font-semibold leading-6 text-gray-900">
        <Link :href="route('dashboard')">
          <ApplicationLogo class="h-8 w-auto" />
        </Link>
      </div>
      <Link :href="route('profile.edit')">
        <span class="sr-only">Your profile</span>
        <UserCircleIcon class="h-8 w-8 rounded-full bg-gray-50 text-gray-300" />
      </Link>
    </div>

    <!-- Mobile sidebar -->
    <div class="lg:hidden">
      <TransitionRoot as="template" :show="mobileMenuOpen">
        <Dialog as="div" class="relative z-50 lg:hidden" @close="mobileMenuOpen = false">
          <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
            <div class="fixed inset-0 bg-gray-900/80" />
          </TransitionChild>

          <div class="fixed inset-0 flex">
            <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
              <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
                <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                  <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" class="-m-2.5 p-2.5" @click="mobileMenuOpen = false">
                      <span class="sr-only">Close sidebar</span>
                      <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                    </button>
                  </div>
                </TransitionChild>
                <!-- Sidebar component, swap this element with another sidebar if you like -->
                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
                  <div class="flex h-16 shrink-0 items-center">
                    <ApplicationLogo class="h-8 w-auto" />
                  </div>
                  <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                      <li>
                        <ul role="list" class="-mx-2 space-y-1">
                          <li v-for="item in navigation" :key="item.name">
                            <Link 
                              :href="item.href" 
                              :class="[
                                item.current
                                  ? 'bg-gray-50 text-indigo-600'
                                  : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600',
                                'group flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6'
                              ]"
                              @click="mobileMenuOpen = false"
                            >
                              <component 
                                :is="item.icon" 
                                :class="[
                                  item.current ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600',
                                  'h-6 w-6 shrink-0'
                                ]" 
                                aria-hidden="true" 
                              />
                              {{ item.name }}
                            </Link>
                          </li>
                        </ul>
                      </li>
                      <li class="mt-auto">
                        <Link 
                          :href="route('profile.edit')" 
                          class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-700 hover:bg-gray-50 hover:text-indigo-600"
                          @click="mobileMenuOpen = false"
                        >
                          <Cog6ToothIcon 
                            class="h-6 w-6 shrink-0 text-gray-400 group-hover:text-indigo-600" 
                            aria-hidden="true" 
                          />
                          Settings
                        </Link>
                      </li>
                    </ul>
                  </nav>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>

    <!-- Main content -->
    <main class="py-10 lg:pl-72">
      <div class="px-4 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
  Dialog, 
  DialogPanel, 
  TransitionChild, 
  TransitionRoot 
} from '@headlessui/vue';
import { 
  Bars3Icon, 
  XMarkIcon,
  HomeIcon,
  HeartIcon,
  ClockIcon,
  ShoppingBagIcon,
  UserCircleIcon,
  Cog6ToothIcon
} from '@heroicons/vue/outline';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const mobileMenuOpen = ref(false);

const navigation = computed(() => {
  const currentRoute = usePage().url;
  
  return [
    { 
      name: 'Dashboard', 
      href: route('dashboard'), 
      icon: HomeIcon,
      current: currentRoute === '/dashboard' 
    },
    { 
      name: 'My Wishlist', 
      href: route('wishlist.index'), 
      icon: HeartIcon,
      current: currentRoute.startsWith('/wishlist')
    },
    { 
      name: 'Recently Viewed', 
      href: route('recently-viewed.index'), 
      icon: ClockIcon,
      current: currentRoute.startsWith('/recently-viewed')
    },
    { 
      name: 'My Orders', 
      href: route('orders.index'), 
      icon: ShoppingBagIcon,
      current: currentRoute.startsWith('/orders')
    },
  ];
});
</script>
