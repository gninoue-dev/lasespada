
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="https://cdn.tailwindcss.com"></script>

</head>
<body>

<section class="py-32">
  <div class="container">
    <div class="flex flex-col gap-4">
      <div class="rounded-lg border bg-card text-card-foreground shadow-sm mx-auto w-full max-w-md">
        <div class="flex flex-col space-y-1.5 p-6 items-center">

          <h3 class="font-semibold tracking-tight text-xl">Log in with your email</h3>
          <p class="text-sm text-zinc-600">Enter your information to login</p>
        </div>
        <div class="p-6 pt-0">
          <div class="grid gap-4">
            <button
              class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-white hover:bg-zinc-100 hover:text-zinc-800 h-10 px-4 py-2 w-full">
              <svg class="h-5 mr-2" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                <path fill="#EA4335"
                  d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z">
                </path>
                <path fill="#4285F4"
                  d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z">
                </path>
                <path fill="#FBBC05"
                  d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z">
                </path>
                <path fill="#34A853"
                  d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z">
                </path>
                <path fill="none" d="M0 0h48v48H0z"></path>
              </svg>Sign up with Google
            </button>
            <div class="flex items-center gap-4">
              <span class="h-px w-full bg-gray-100"></span><span class="text-xs text-zinc-600">OR</span><span
                class="h-px w-full bg-gray-100"></span>
            </div>
            <div class="grid gap-2">
              <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                for="email">Email</label><input type="email"
                class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                id="email" placeholder="m@example.com" required="" />
            </div>
            <div class="grid gap-2">
              <div class="flex justify-between">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="password">Password</label><a href="#" class="text-sm underline">Forgot password</a>
              </div>
              <input type="password"
                class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                id="password" placeholder="Enter your password" required="" />
            </div>
            <button
              class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-zinc-900 text-white hover:bg-zinc-900/90 h-10 px-4 py-2 w-full"
              type="submit">
              Log in
            </button>
          </div>
        </div>
      </div>
      <div class="mx-auto flex gap-1 text-sm">
        <p>Don&#x27;t have an account yet?</p>
        <a href="#" class="underline">Log in</a>
      </div>
    </div>
  </div>
</section>
  
</body>
</html>
