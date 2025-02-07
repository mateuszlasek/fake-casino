<script setup>
import { Link, Head } from '@inertiajs/vue3';
import { LogIn, UserPlus } from 'lucide-vue-next';
</script>

<template>
  <div>
    <div class="min-h-screen bg-casino-1">
      <header class="bg-casino-2 text-white">
        <nav class="flex items-center justify-between p-4 max-w-screen-lg mx-auto">
          <div class="space-x-6">
            <Link href="/">Home</Link>
            <Link href="/about">About</Link>
            <Link href="/casino">Casino</Link>
            <Link href="/contact">Contact</Link>
          </div>
          <div class="space-x-4 flex items-center">
            <Link href="/login" class="flex items-center space-x-2 hover:text-yellow-400">
              <LogIn class="w-5 h-5" />
              <span>Login</span>
            </Link>
            <Link href="/register" class="flex items-center space-x-2 hover:text-yellow-400">
              <UserPlus class="w-5 h-5" />
              <span>Register</span>
            </Link>
          </div>
        </nav>
      </header>

      <main class="p-4">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
</style>
