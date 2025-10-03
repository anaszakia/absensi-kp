@extends('layouts.app')

@section('title', 'Dashboard - Catalyst')

@section('page-title', 'Good afternoon, Erica')

@section('content')
    <!-- Overview Section -->
    <div class="mb-8">
        <h1 class="text-lg font-bold text-gray-900">Hallo, Selamat Datang di Dashboard !</h1>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Overview</h2>
            <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                <option>Last week</option>
                <option>Last month</option>
                <option>Last 3 months</option>
                <option>Last year</option>
            </select>
        </div>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Revenue -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">$2.6M</p>
                        <div class="flex items-center mt-2">
                            <span class="text-sm text-green-600 font-medium">+4.5%</span>
                            <span class="text-sm text-gray-500 ml-2">from last week</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <!-- Average Order Value -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Average order value</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">$455</p>
                        <div class="flex items-center mt-2">
                            <span class="text-sm text-red-600 font-medium">-0.5%</span>
                            <span class="text-sm text-gray-500 ml-2">from last week</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <!-- Tickets Sold -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Tickets sold</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">5,888</p>
                        <div class="flex items-center mt-2">
                            <span class="text-sm text-green-600 font-medium">+4.5%</span>
                            <span class="text-sm text-gray-500 ml-2">from last week</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-ticket-alt text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <!-- Pageviews -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pageviews</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">823,067</p>
                        <div class="flex items-center mt-2">
                            <span class="text-sm text-green-600 font-medium">+21.2%</span>
                            <span class="text-sm text-gray-500 ml-2">from last week</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-eye text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Order number
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Purchase date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Customer
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Event
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            3000
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            May 9, 2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Leslie Alexander
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-music text-white text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">Bear Hug: Live in Concert</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            US$80.00
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            3001
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            May 5, 2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Michael Foster
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-headphones text-white text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">Six Fingers — DJ Set</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            US$299.00
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            3002
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Apr 28, 2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Dries Vincent
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-teal-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-guitar text-white text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">We All Look The Same</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            US$150.00
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            3003
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Apr 23, 2024
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Lindsay Walton
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-pink-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-microphone text-white text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">Bear Hug: Live in Concert</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            US$80.00
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection