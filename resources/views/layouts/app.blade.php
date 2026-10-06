<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- ĐÃ THÊM CSRF TOKEN THEO LAB07 -->
    <title>Web An Tâm - Quản Trị</title>
    <!-- Bootstrap & FontAwesome -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .nav-link-btn { background: none; border: none; padding: 0; color: #dc3545; cursor: pointer; font-weight: bold; }
        
        /* GIAO DIỆN SIDEBAR BÊN TRÁI HIỆN ĐẠI */
        .admin-sidebar { width: 260px; height: 100vh; background: #ffffff; position: fixed; top: 0; left: 0; border-right: 1px solid #dee2e6; padding-top: 20px; z-index: 1000; }
        .admin-sidebar .brand-title { font-size: 1.2rem; font-weight: bold; color: #17a2b8; text-align: center; margin-bottom: 30px; text-transform: uppercase; letter-spacing: 1px; }
        .admin-sidebar a { color: #495057; padding: 12px 25px; display: block; text-decoration: none; font-weight: 500; transition: all 0.2s; }
        .admin-sidebar a i { width: 25px; color: #6c757d; }
        .admin-sidebar a:hover, .admin-sidebar a.active { background: #e9ecef; color: #007bff; border-left: 4px solid #007bff; }
        .admin-sidebar a:hover i { color: #007bff; }
        
        /* KHUNG NỘI DUNG CHÍNH BÊN PHẢI (DÀNH CHO ADMIN) */
        .admin-wrapper { margin-left: 260px; }
        .admin-topbar { background: #ffffff; padding: 15px 30px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }
        .admin-content { padding: 30px; }

        @media all and (min-width: 992px) {
            .custom-dropdown-hover:hover .dropdown-menu { display: block; margin-top: 0; transition: all 0.3s ease; }
        }
        .dropdown-item:hover { background-color: #f8f9fa; color: #17a2b8; font-weight: bold; }

        /* CSS KHUNG CHAT TỪ LAB07 */
        #chat-box, #admin-chat-box { position: fixed; bottom: 20px; right: 20px; z-index: 1050; }
        #chat-popup { width: 320px; bottom: 70px; right: 20px; position: fixed; display: none; }
        #chat-messages { height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; overflow-x: hidden; }
        #user-list { max-height: 150px; overflow-y: auto; border-bottom: 1px solid #ddd; }
        .user-item { padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; }
        .user-item:hover, .user-item.active { background: #e9ecef; color: #007bff; font-weight: bold; }
    </style>
</head>
<body>

    @if(request()->is('admin*'))
        <div class="admin-sidebar shadow-sm">
            <div class="brand-title">WEB AN TÂM</div>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}"><i class="fa-solid fa-print"></i> Quản lý Máy In</a>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}"><i class="fa-solid fa-list"></i> Quản lý Danh Mục</a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Quản lý Tài Khoản</a>
            <a href="{{ url('admin/orders') }}" class="{{ request()->is('admin/orders*') ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping"></i> Quản lý Đơn hàng</a>
            
            <hr class="mx-3 my-2">
            
            <!-- Module Báo cáo kinh doanh (Cũ) -->
            <a href="{{ url('admin/reports') }}" class="{{ request()->is('admin/reports*') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie"></i> Báo cáo Thống kê</a>
            
            <!-- Module Tài chính & Giao dịch (Mới) -->
            <a href="{{ route('admin.finance.index') }}" class="{{ request()->routeIs('admin.finance.index') ? 'active' : '' }}"><i class="fa-solid fa-coins"></i> Thống kê Tài chính</a>
            <a href="{{ route('admin.finance.transactions') }}" class="{{ request()->routeIs('admin.finance.transactions') ? 'active' : '' }}"><i class="fa-solid fa-money-bill-transfer"></i> Giao dịch Thanh toán</a>
            
            <hr class="mx-3 my-4">
            <a href="{{ route('welcome') }}"><i class="fa-solid fa-globe"></i> Xem Website</a>
        </div>
        
        <div class="admin-wrapper">
            <div class="admin-topbar shadow-sm">
                <h5 class="m-0 text-dark font-weight-bold">Hệ Thống Quản Trị</h5>
                <div class="d-flex align-items-center">
                    <span class="mr-3 font-weight-bold text-success">Xin chào, {{ Auth::user()->name ?? 'Admin' }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger font-weight-bold">Đăng xuất</button>
                    </form>
                </div>
            </div>
            
            <div class="admin-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

        <!-- HTML ADMIN CHAT -->
        <div id="admin-chat-box">
            <button id="chat-toggle" class="btn btn-dark shadow"> Chat Khách hàng</button>
            <div id="chat-popup" class="card shadow-lg" style="display: none; border-radius: 10px;">
                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <strong>Hỗ trợ trực tuyến</strong>
                    <button id="chat-close" class="btn btn-sm btn-light">X</button>
                </div>
                <div id="user-list">
                    <div class="p-2 text-center text-muted"><small>Đang tải danh sách...</small></div>
                </div>
                <div id="chat-messages">
                    <div class="text-center mt-5 text-muted">Chọn một khách hàng để xem tin nhắn</div>
                </div>
                <div class="card-footer bg-white">
                    <div class="input-group">
                        <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Nhập câu trả lời...">
                        <div class="input-group-append">
                            <button id="send-btn" class="btn btn-success btn-sm">Gửi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand font-weight-bold text-info" style="font-size: 1.5rem;" href="{{ route('welcome') }}">WEB AN TÂM</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="{{ route('welcome') }}">Trang chủ</a></li>
                        <li class="nav-item dropdown custom-dropdown-hover">
                            <a class="nav-link dropdown-toggle" href="#" id="danhMucDropdown" role="button" aria-expanded="false">Danh mục</a>
                            <div class="dropdown-menu shadow border-0" aria-labelledby="danhMucDropdown">
                                <a class="dropdown-item" href="#">Máy in Canon</a>
                                <a class="dropdown-item" href="#">Máy in HP</a>
                                <a class="dropdown-item" href="#">Máy in Epson</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Giấy in & Mực in</a>
                            </div>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                    </ul>
                    <ul class="navbar-nav ml-auto">
                        @auth
                            @if(Auth::user()->role == 1)
                                <li class="nav-item mr-3"><a class="nav-link font-weight-bold text-primary" href="{{ route('products.index') }}">Vào trang Quản trị</a></li>
                            @endif
                            <li class="nav-item mr-3">
                                <a class="nav-link font-weight-bold" style="color: #fd7e14;" href="{{ route('user.orders.index') }}">
                                    <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử đơn hàng
                                </a>
                            </li>
                            <li class="nav-item d-flex align-items-center mr-3">
                                <span class="navbar-text font-weight-bold text-success">Xin chào, {{ Auth::user()->name }}</span>
                            </li>
                            <li class="nav-item d-flex align-items-center">
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="nav-link-btn nav-link">Đăng xuất</button>
                                </form>
                            </li>
                        @endauth
                        @guest
                            <li class="nav-item"><a class="nav-link text-primary font-weight-bold" href="{{ route('login') }}">Đăng nhập</a></li>
                            <li class="nav-item"><a class="nav-link border border-info rounded text-info ml-2 px-3" href="{{ route('register') }}">Đăng ký</a></li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-4 mb-5">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @yield('content')
        </div>

        <!-- HTML USER CHAT -->
        @auth
        <div id="chat-box">
            <button id="chat-toggle" class="btn btn-primary rounded-circle shadow p-3"><i class="fa-solid fa-message"></i> Chat</button>
            <div id="chat-popup" class="card shadow-lg" style="display: none; border-radius: 10px;">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Hỗ trợ khách hàng</span>
                    <button id="chat-close" class="btn btn-sm btn-light">X</button>
                </div>
                <div id="chat-messages" class="card-body" style="height: 300px; overflow-y: auto; background: #f8f9fa; overflow-x: hidden;">
                    <small class="text-muted">Đang tải lịch sử...</small>
                </div>
                <div class="card-footer bg-white">
                    <div class="input-group">
                        <input type="text" id="chat-input" class="form-control" placeholder="Nhập tin nhắn..." autocomplete="off">
                        <div class="input-group-append">
                            <button id="send-btn" class="btn btn-success">Gửi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endauth

    @endif

    <!-- SCRIPT CHUNG -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <!-- LOGIC SCRIPT XỬ LÝ CHAT -->
    @if(request()->is('admin*'))
        <!-- SCRIPT ADMIN CHAT TỪ LAB07 -->
        <script>
            let currentUserId = null;
            const chatPopup = document.getElementById("chat-popup");
            const chatMessages = document.getElementById("chat-messages");
            const chatInput = document.getElementById("chat-input");

            document.getElementById("chat-toggle").onclick = () => {
                chatPopup.style.display = "block";
                loadUsers();
            };
            document.getElementById("chat-close").onclick = () => {
                chatPopup.style.display = "none";
            };

            function loadUsers() {
                fetch("{{ route('admin.chat.users') }}")
                .then(res => res.json())
                .then(users => {
                    let html = "";
                    users.forEach(user => {
                        let activeClass = (currentUserId == user.id) ? 'active' : '';
                        html += `<div class="user-item ${activeClass}" onclick="selectUser(${user.id}, this)">${user.name}</div>`;
                    });
                    document.getElementById("user-list").innerHTML = html || '<div class="p-2 text-muted">Chưa có hội thoại</div>';
                });
            }

            function selectUser(userId, element) {
                currentUserId = userId;
                document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
                element.classList.add('active');
                loadMessages();
            }

            function loadMessages() {
                if (!currentUserId) return;
                fetch(`/admin/chat/messages/${currentUserId}`)
                .then(res => res.json())
                .then(messages => {
                    let html = "";
                    messages.forEach(msg => {
                        // Ép kiểu đảm bảo kiểm tra đúng ID Admin là 1
                        let isAdmin = (parseInt(msg.sender_id) === 1); 
                        
                        let alignFlex = isAdmin ? "justify-content-end" : "justify-content-start";
                        let bgColor = isAdmin ? "bg-primary text-white" : "bg-light text-dark";
                        let textAlign = isAdmin ? "text-right" : "text-left";
                        let senderName = isAdmin ? "Bạn" : (msg.sender ? msg.sender.name : "Khách");
                        
                        html += `
                            <div class="d-flex ${alignFlex} mb-2 w-100">
                                <div style="max-width: 80%;">
                                    <div class="${textAlign} text-muted" style="font-size: 11px; margin-bottom: 2px;">
                                        ${senderName}
                                    </div>
                                    <div class="p-2 shadow-sm ${bgColor}" style="border-radius: 12px; word-wrap: break-word;">
                                        ${msg.content}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    chatMessages.innerHTML = html;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
            }

            function sendMessage() {
                let message = chatInput.value.trim();
                if (!message || !currentUserId) return;
                
                fetch(`/admin/chat/send/${currentUserId}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: message,
                        user_id: currentUserId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    chatInput.value = "";
                    loadMessages();
                })
                .catch(err => console.error("Lỗi gửi tin:", err));
            }

            document.getElementById("send-btn").onclick = sendMessage;
            chatInput.onkeypress = (e) => { if(e.key === 'Enter') sendMessage(); };

            setInterval(() => {
                if (chatPopup.style.display === "block") {
                    loadMessages();
                    loadUsers();
                }
            }, 3000);
        </script>
    @else
        <!-- SCRIPT USER CHAT TỪ LAB07 -->
        @auth
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const toggleBtn = document.getElementById("chat-toggle");
                const chatPopup = document.getElementById("chat-popup");
                const closeBtn = document.getElementById("chat-close");
                const sendBtn = document.getElementById("send-btn");
                const input = document.getElementById("chat-input");
                const chatBox = document.getElementById("chat-messages");
                
                if (!toggleBtn) return; 

                toggleBtn.onclick = () => {
                    chatPopup.style.display = "block";
                    toggleBtn.style.display = "none";
                    loadMessages();
                };
                
                closeBtn.onclick = () => {
                    chatPopup.style.display = "none";
                    toggleBtn.style.display = "block";
                };

                function loadMessages() {
                    fetch("{{ route('user.chat.messages') }}")
                    .then(res => res.json())
                    .then(messages => {
                        let html = "";
                        if(messages.length === 0) {
                            html = "<div class='text-center text-muted'><small>Bắt đầu cuộc trò chuyện với Admin</small></div>";
                        }
                        messages.forEach(msg => {
                            // Khách hàng thì ID khác 1 là tin nhắn của chính họ
                            let isMe = (parseInt(msg.sender_id) !== 1); 
                            
                            let alignFlex = isMe ? "justify-content-end" : "justify-content-start";
                            let bgColor = isMe ? "bg-primary text-white" : "bg-light text-dark";
                            let textAlign = isMe ? "text-right" : "text-left";
                            let senderName = isMe ? "Bạn" : "Admin";
                            
                            html += `
                                <div class="d-flex ${alignFlex} mb-2 w-100">
                                    <div style="max-width: 80%;">
                                        <div class="${textAlign} text-muted" style="font-size: 11px; margin-bottom: 2px;">
                                            ${senderName}
                                        </div>
                                        <div class="p-2 shadow-sm ${bgColor}" style="border-radius: 12px; word-wrap: break-word;">
                                            ${msg.content}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        chatBox.innerHTML = html;
                        chatBox.scrollTop = chatBox.scrollHeight;
                    })
                    .catch(err => console.error("Lỗi tải tin nhắn:", err));
                }

                function sendMessage() {
                    let message = input.value.trim();
                    if (message === "") return;
                    
                    input.disabled = true;
                    sendBtn.disabled = true;
                    
                    fetch("{{ route('user.chat.send') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            "Content-Type": "application/json",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({ message: message })
                    })
                    .then(res => res.json())
                    .then(data => {
                        input.value = "";
                        input.disabled = false;
                        sendBtn.disabled = false;
                        input.focus();
                        loadMessages(); 
                    })
                    .catch(err => {
                        console.error("Lỗi gửi tin:", err);
                        input.disabled = false;
                        sendBtn.disabled = false;
                    });
                }

                sendBtn.onclick = sendMessage;
                input.addEventListener("keypress", function(e) {
                    if (e.key === "Enter") {
                        sendMessage();
                    }
                });

                setInterval(() => {
                    if (chatPopup.style.display === "block") {
                        loadMessages();
                    }
                }, 3000);
            });
        </script>
        @endauth
    @endif
</body>
</html>