<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>OVCS</title>
        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/output.css') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Serif:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
        <style>
            
            /* resources/views/tailwindcss-landing-gradients-master/static */

        </style>
        
    </head>
    <body class="bg-gradient-to-br from-gray-900 to-black">
        <div
          class="container p-8 mx-auto overflow-hidden text-gray-300 md:rounded-lg md:p-10 lg:p-12"
        >
          <div class="flex justify-between">
            <h1 class="font-serif text-3xl font-medium">Online Village Census System</h1>
            <a
              href="{{ url('/admin') }}"
              class="self-start px-3 py-2 leading-none text-gray-200 border border-gray-800 rounded-lg focus:outline-none focus:shadow-outline bg-gradient-to-b hover:from-indigo-500 from-gray-900 to-black"
            >
              Login
            </a>
          </div>
    
          <div class="h-32 md:h-40"></div>
    
          <p
            class="max-w-5xl font-sans text-4xl font-bold text-gray-200 lg:text-7xl lg:pr-24 md:text-6xl"
          >
          Empowering Communities Through Data
          </p>
          <div class="h-10"></div>
          <p class="max-w-2xl font-serif text-xl text-gray-400 md:text-2xl">
            Welcome to the Online Village Census System, your trusted platform for collecting, managing, and analyzing village census data. 
            Our mission is to provide accurate and up-to-date information to support community planning, development, and resource allocation.
          </p>
    
          <div class="h-32 md:h-40"></div>
    
          <div class="grid gap-8 md:grid-cols-2">
            <div class="flex flex-col justify-center">
              <p
                class="self-start inline font-sans text-xl font-medium text-transparent bg-clip-text bg-gradient-to-br from-green-400 to-green-600"
              >
                Simple and easy
              </p>
              <h2 class="text-4xl font-bold">Key Features:</h2>
              <div class="h-6"></div>
              <div class="h-8"></div>
              <div class="grid grid-cols-2 gap-4 pt-8 border-t border-gray-800">
                <div>
                  <p class="font-semibold text-gray-400">Comprehensive Data Collection</p>
                  <div class="h-4"></div>
                  <p class="font-serif text-gray-400">
                    Gather detailed demographic, economic, and social data from every household in the village.
                  </p>
                </div>
                <div>
                  <p class="font-semibold text-gray-400">User-Friendly Interface</p>
                  <div class="h-4"></div>
                  <p class="font-serif text-gray-400">
                    Easily navigate through our intuitive platform designed for users of all technical backgrounds.
                  </p>
                </div>
                <div>
                    <p class="font-semibold text-gray-400">Secure and Confidential</p>
                    <div class="h-4"></div>
                    <p class="font-serif text-gray-400">
                        Your data privacy is our top priority. We ensure all information is securely stored and handled with the utmost confidentiality.
                    </p>
                </div>
                <div>
                    <p class="font-semibold text-gray-400">Community Engagement</p>
                    <div class="h-4"></div>
                    <p class="font-serif text-gray-400">
                        Encourage active participation from villagers through user-friendly surveys and feedback mechanisms.
                    </p>
                </div>
                
              </div>
            </div>
            <div>
              <div
                class="-mr-24 rounded-lg md:rounded-l-full bg-gradient-to-br from-gray-900 to-black h-96"
              ></div>
            </div>
          </div>
    
          <div class="h-32 md:h-40"></div>
    
          <p class="font-serif text-4xl">
            <span class="text-gray-400">If we work all together</span>
    
            <span class="text-gray-600"
              >consectetur adipisicing elit. Consectetur atque molestiae omnis
              excepturi enim!</span
            >
          </p>
    
          <div class="h-32 md:h-40"></div>
    
          <div class="grid gap-4 md:grid-cols-3">
            <div
              class="flex-col p-8 py-16 rounded-lg shadow-2xl md:p-12 bg-gradient-to-br from-gray-900 to-black"
            >
              <p
                class="flex items-center justify-center text-4xl font-semibold text-green-400 bg-green-800 rounded-full shadow-lg w-14 h-14"
              >
                1
              </p>
              <div class="h-6"></div>
              <p class="font-serif text-3xl">dedicated to transforming how villages collect and utilize data</p>
            </div>
            <div
              class="flex-col p-8 py-16 rounded-lg shadow-2xl md:p-12 bg-gradient-to-b from-gray-900 to-black"
            >
              <p
                class="flex items-center justify-center text-4xl font-semibold text-indigo-400 bg-indigo-800 rounded-full shadow-lg w-14 h-14"
              >
                2
              </p>
              <div class="h-6"></div>
              <p class="font-serif text-3xl">
                To create a connected and informed community.
              </p>
            </div>
            <div
              class="flex-col p-8 py-16 rounded-lg shadow-2xl md:p-12 bg-gradient-to-bl from-gray-900 to-black"
            >
              <p
                class="flex items-center justify-center text-4xl font-semibold text-teal-400 bg-teal-800 rounded-full shadow-lg w-14 h-14"
              >
                3
              </p>
              <div class="h-6"></div>
              <p class="font-serif text-3xl">We made it simple and easy to do</p>
            </div>
          </div>
    
          <div class="h-40"></div>
    
          <div class="grid gap-8 md:grid-cols-3">
            <div class="flex flex-col justify-center md:col-span-2">
              <p
                class="self-start inline font-sans text-xl font-medium text-transparent bg-clip-text bg-gradient-to-br from-teal-400 to-teal-600"
              >
                We are humans
              </p>
              <h2 class="text-4xl font-bold">We could work together</h2>
              <div class="h-6"></div>
              <p class="font-serif text-xl text-gray-400 md:pr-10">
                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Magnam
                autem, a recusandae vero praesentium qui impedit doloremque
                molestias.
              </p>
              <div class="h-8"></div>
              <div class="grid gap-6 pt-8 border-t border-gray-800 lg:grid-cols-3">
                <div>
                  <p class="font-semibold text-gray-400">Made with love</p>
                  <div class="h-4"></div>
                  <p class="font-serif text-gray-400">
                    Lorem ipsum dolor sit, amet consectetur adipisicing elit.
                    Delectus labor.
                  </p>
                </div>
                <div>
                  <p class="font-semibold text-gray-400">It's easy to build</p>
                  <div class="h-4"></div>
                  <p class="font-serif text-gray-400">
                    Ipsum dolor sit, amet consectetur adipisicing elit. Delectus
                    amet consectetur.
                  </p>
                </div>
                <div>
                  <p class="font-semibold text-gray-400">It's easy to build</p>
                  <div class="h-4"></div>
                  <p class="font-serif text-gray-400">
                    Ipsum dolor sit, amet consectetur adipisicing elit. Delectus
                    amet consectetur.
                  </p>
                </div>
              </div>
            </div>
            <div>
              <div
                class="-mr-24 rounded-lg md:rounded-l-full bg-gradient-to-br from-gray-900 to-black h-96"
              ></div>
            </div>
          </div>
    
          <div class="h-10 md:h-40"></div>
    
          <div class="grid gap-4 md:grid-cols-4">
            <ul class="space-y-1 text-gray-400">
              <li class="pb-4 font-serif text-gray-400">Social networks</li>
              <li>
                <a href="https://twitter.com/victormustar" class="hover:underline"
                  >Twitter</a
                >
              </li>
              <li>
                <a href="#" class="hover:underline">Linkedin</a>
              </li>
              <li>
                <a href="#" class="hover:underline">Facebook</a>
              </li>
            </ul>
            <ul class="space-y-1 text-gray-400">
              <li class="pb-4 font-serif text-gray-400">Locations</li>
              <li>
                <a href="#" class="hover:underline">Paris</a>
              </li>
              <li>
                <a href="#" class="hover:underline">New York</a>
              </li>
              <li>
                <a href="#" class="hover:underline">London</a>
              </li>
              <li>
                <a href="#" class="hover:underline">Singapour</a>
              </li>
            </ul>
            <ul class="space-y-1 text-gray-400">
              <li class="pb-4 font-serif text-gray-400">Company</li>
              <li>
                <a href="#" class="hover:underline">The team</a>
              </li>
              <li>
                <a href="#" class="hover:underline">About us</a>
              </li>
              <li>
                <a href="#" class="hover:underline">Our vision</a>
              </li>
              <li>
                <a href="#" class="hover:underline">Join us</a>
              </li>
            </ul>
            <ul class="space-y-1 text-gray-400">
              <li class="pb-4 font-serif text-gray-400">Join</li>
              <li>
                <a
                  href="{{ url('/admin') }}"
                  class="self-start px-3 py-2 leading-none text-gray-200 border border-gray-800 rounded-lg focus:outline-none focus:shadow-outline bg-gradient-to-b hover:from-indigo-500 from-gray-900 to-black"
                >
                  Login
                </a>
              </li>
            </ul>
          </div>
          <div class="h-12"></div>
        </div>
      </body>
</html>
