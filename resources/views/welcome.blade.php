<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Legal Assistant - Smart Legal Case Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563EB;
            --primary-light: #3B82F6;
            --primary-dark: #1D4ED8;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .feature-card {
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
            box-shadow: 0 20px 25px -5px rgba(37, 99, 235, 0.1);
        }
        
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--primary-color);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="h-full">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-md z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-balance-scale text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">AI Legal Assistant</h1>
                        <p class="text-xs text-gray-500">Professional Suite</p>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Features</a>
                    <a href="#how-it-works" class="nav-link text-gray-700 hover:text-blue-600 font-medium">How It Works</a>
                    <a href="#pricing" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Pricing</a>
                    <a href="#testimonials" class="nav-link text-gray-700 hover:text-blue-600 font-medium">Testimonials</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium hidden md:block">Sign In</a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-2 rounded-xl font-medium hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                        Get Started Free
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Hero Content -->
                <div class="text-white">
                    <h1 class="text-4xl md:text-6xl font-bold leading-tight mb-6">
                        Smart Legal Case Management
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-pink-300">
                            Powered by AI
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-200 mb-8 leading-relaxed">
                        Streamline your legal practice with intelligent case management, automated documentation, and AI-powered insights.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all shadow-2xl hover:shadow-2xl text-center">
                            <i class="fas fa-rocket mr-2"></i>Start Free Trial
                        </a>
                        <a href="#features" class="border-2 border-white text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white hover:text-blue-600 transition-all text-center">
                            <i class="fas fa-play-circle mr-2"></i>Watch Demo
                        </a>
                    </div>
                    <div class="flex items-center space-x-6 text-sm text-gray-200">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>No credit card required</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>14-day free trial</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-check-circle text-green-300"></i>
                            <span>Cancel anytime</span>
                        </div>
                    </div>
                </div>

                <!-- Hero Visual -->
                <div class="relative">
                    <div class="bg-white/20 backdrop-blur-lg rounded-2xl p-8 border border-white/30 shadow-2xl">
                        <div class="bg-white rounded-xl p-6 shadow-2xl">
                            <div class="flex items-center space-x-3 mb-6">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-robot text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900">AI Case Analysis</h3>
                                    <p class="text-sm text-gray-600">Processing your legal documents...</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">Case Similarity</span>
                                    <span class="text-sm font-bold text-blue-600">92% Match</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">Success Probability</span>
                                    <span class="text-sm font-bold text-green-600">85%</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">Document Review</span>
                                    <span class="text-sm font-bold text-purple-600">Completed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -left-4 w-24 h-24 bg-yellow-400 rounded-full blur-xl opacity-20 animate-pulse"></div>
                    <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-pink-400 rounded-full blur-xl opacity-20 animate-pulse delay-1000"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">
                    Powerful Features for
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-600">
                        Modern Law Practices
                    </span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to manage your legal practice efficiently with AI-powered tools and automation.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-folder text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Smart Case Management</h3>
                    <p class="text-gray-600 mb-4">
                        Organize and track all your cases with intelligent categorization, status updates, and automated reminders.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Automated case tracking</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Document management</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Hearing calendar</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-robot text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">AI-Powered Analysis</h3>
                    <p class="text-gray-600 mb-4">
                        Get intelligent insights, precedent analysis, and success probability predictions for your cases.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Case similarity analysis</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Legal document review</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Success probability</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-users text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Client Management</h3>
                    <p class="text-gray-600 mb-4">
                        Manage client information, communication, and billing all in one centralized platform.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Client portal access</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Secure messaging</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Billing & invoicing</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-file-contract text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Document Automation</h3>
                    <p class="text-gray-600 mb-4">
                        Generate legal documents, contracts, and filings automatically with customizable templates.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Template library</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Auto-fill forms</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>E-signature integration</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-bar text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Analytics & Reporting</h3>
                    <p class="text-gray-600 mb-4">
                        Track your firm's performance with detailed analytics, revenue reports, and case success metrics.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Performance dashboard</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Revenue tracking</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Custom reports</span>
                        </li>
                    </ul>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-white rounded-2xl p-8 shadow-lg">
                    <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Security & Compliance</h3>
                    <p class="text-gray-600 mb-4">
                        Enterprise-grade security with encryption, access controls, and compliance with legal standards.
                    </p>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>End-to-end encryption</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>Two-factor authentication</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <i class="fas fa-check text-green-500 text-sm"></i>
                            <span>GDPR & HIPAA compliant</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">
                    How It
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-600">
                        Works
                    </span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Get started in minutes and transform your legal practice with our simple 4-step process.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-blue-600">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Sign Up</h3>
                    <p class="text-gray-600">
                        Create your account in 30 seconds. No credit card required for the free trial.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-green-600">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Add Your Cases</h3>
                    <p class="text-gray-600">
                        Import existing cases or create new ones with our intuitive case management system.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">AI Analysis</h3>
                    <p class="text-gray-600">
                        Let our AI analyze your cases and provide insights, predictions, and recommendations.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-20 h-20 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <span class="text-2xl font-bold text-red-600">4</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Manage & Grow</h3>
                    <p class="text-gray-600">
                        Use our tools to manage your practice efficiently and grow your client base.
                    </p>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="text-center mt-16">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-12 text-white">
                    <h3 class="text-3xl md:text-4xl font-bold mb-4">Ready to Transform Your Legal Practice?</h3>
                    <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                        Join thousands of legal professionals who are already using AI Legal Assistant to streamline their work.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all shadow-2xl">
                            Start Free Trial
                        </a>
                        <a href="#features" class="border-2 border-white text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white hover:text-blue-600 transition-all">
                            Schedule Demo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 mb-4">
                    What Our
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-600">
                        Clients Say
                    </span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Don't just take our word for it. Here's what legal professionals are saying about AI Legal Assistant.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="font-bold text-blue-600">SA</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Sarah Ahmed</h4>
                            <p class="text-sm text-gray-600">Senior Partner, Corporate Law</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">
                        "AI Legal Assistant has transformed how we manage our corporate cases. The AI analysis has helped us predict case outcomes with 90% accuracy."
                    </p>
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="font-bold text-green-600">MK</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Michael Khan</h4>
                            <p class="text-sm text-gray-600">Criminal Defense Attorney</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">
                        "The document automation and case management features have saved me 15+ hours per week. My productivity has increased significantly."
                    </p>
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-lg">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="font-bold text-purple-600">RJ</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Dr. Rizwan Javed</h4>
                            <p class="text-sm text-gray-600">Family Law Specialist</p>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">
                        "The client management portal has improved our communication and made document sharing seamless. Our clients love the transparency."
                    </p>
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-balance-scale text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">AI Legal Assistant</h1>
                            <p class="text-xs text-gray-400">Professional Suite</p>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Transforming legal practices with AI-powered tools and intelligent case management solutions.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook"></i>
                        </a>
                    </div>
                </div>

                <!-- Product -->
                <div>
                    <h3 class="font-bold text-lg mb-6">Product</h3>
                    <ul class="space-y-3">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#how-it-works" class="text-gray-400 hover:text-white transition-colors">How It Works</a></li>
                        <li><a href="#pricing" class="text-gray-400 hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h3 class="font-bold text-lg mb-6">Resources</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Community</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="font-bold text-lg mb-6">Contact</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center space-x-2 text-gray-400">
                            <i class="fas fa-envelope"></i>
                            <span>support@ailegal.com</span>
                        </li>
                        <li class="flex items-center space-x-2 text-gray-400">
                            <i class="fas fa-phone"></i>
                            <span>+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-center space-x-2 text-gray-400">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>New York, NY 10001</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2024 AI Legal Assistant. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('bg-white', 'shadow-lg');
                nav.classList.remove('bg-white/90');
            } else {
                nav.classList.remove('bg-white', 'shadow-lg');
                nav.classList.add('bg-white/90');
            }
        });
    </script>
</body>
</html>