<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>SisaRasa - Chat</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      background: white;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: bottom;
      padding: 10px;
      border-bottom: 1px solid #e2e2e2;
    }
    .navbar h1 {
      font-family: 'Inter', sans-serif;
      font-weight: 800;
      color: #0B3D3B;
      font-size: 30px;
    }
    .navbar-center {
      display: flex;
      gap: 30px;
      align-items: center;
      justify-content: flex-start;
      margin-right: auto;
      margin-left: 205px;
    }
    .navbar-center span {
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      font-size: 15px;
      cursor: pointer;
      color: #333;
    }
    .top-icons {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .top-icons i {
      font-size: 20px;
      cursor: pointer;
      color: #000;
      padding: 0 5px;
    }
    .chat-container {
      display: flex;
      height: calc(100vh - 70px);
    }
    .sidebar {
      width: 350px;
      border-right: 1px solid #ddd;
      padding: 20px;
    }
    .sidebar h2 {
      font-family: 'Roboto', sans-serif;
      font-weight: 800;
      font-size: 20px;
      margin-bottom: 10px;
      color: #0B3D3B;
      text-align: center;
    }
    .search-bar {
      background: #f0f0f0;
      padding: 10px 15px;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    .chat-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .chat-item {
      display: flex;
      gap: 15px;
      align-items: center;
      background: white;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid transparent;
      cursor: pointer;
      border-bottom: 1px solid #e2e2e2;
    }
    .chat-item.active {
      background: #E8F7F5;
      border-left: 4px solid #0B3D3B;
    }
    .chat-item img {
      width: 40px;
    }
    .chat-item .text {
      font-size: 13px;
      color: #555;
    }
    .main-chat {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .chat-header {
      background: #0B3D3B;
      padding: 15px 25px;
      display: flex;
      align-items: center;
      gap: 15px;
      font-weight: 600;
      font-size: 18px;
      color: white;
    }
    .chat-header img {
      width: 30px;
      border-radius: 50%;
    }
    .chat-header .top-icons i {
      color: white;
    }
    .chat-header .top-icons {
      margin-left: auto;
      gap: 15px;
    }
    .chat-body {
      flex: 1;
      padding: 30px 50px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .chat-date {
      align-self: center;
      background: #0B3D3B;
      color: white;
      padding: 3px 10px;
      border-radius: 10px;
      font-size: 12px;
    }
    .message-wrapper {
      display: flex;
      align-items: flex-end;
      gap: 10px;
      max-width: 60%;
    }
    .message-wrapper.incoming {
      align-self: flex-start;
      flex-direction: row;
    }
    .message-wrapper.outgoing {
      align-self: flex-end;
      flex-direction: row-reverse;
    }
    .message {
      padding: 12px 16px;
      border-radius: 15px;
      font-size: 14px;
      line-height: 1.4;
    }
    .message.incoming {
      background: #f1f1f1;
      color: #333;
    }
    .message.outgoing {
      background: #0B3D3B;
      color: white;
    }
    .avatar {
      width: 28px;
      height: 28px;
      border-radius: 50%;
    }
    .timestamp {
      font-size: 11px;
      color: gray;
      margin-top: 4px;
      display: block;
    }
    .checkmarks {
      font-size: 12px;
      color: #34B7F1;
    }
    .message-input {
      display: flex;
      align-items: center;
      border-top: 1px solid #ddd;
      padding: 15px 25px;
      gap: 10px;
    }
    .message-input input {
      flex: 1;
      padding: 10px 15px;
      border: none;
      background: #f0f0f0;
      border-radius: 20px;
    }
    .message-input .icon-btn {
      background: #0B3D3B;
      color: white;
      border: none;
      border-radius: 50%;
      padding: 10px 12px;
      cursor: pointer;
    }
    .language-dropdown {
      position: relative;
    }
    .flag-icon {
      width: 25px;
      border-radius: 4px;
      cursor: pointer;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      background-color: white;
      min-width: 60px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
      padding: 5px 10px;
      z-index: 1;
      border-radius: 6px;
      top: 30px;
    }
    .dropdown-content img {
      width: 25px;
      cursor: pointer;
    }
    .language-dropdown:hover .dropdown-content {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>SisaRasa</h1>
    <div class="navbar-center">
      <span>Beranda</span>
      <span>Menu</span>
      <span>Lokasi</span>
      <span>Riwayat</span>
    </div>
    <div class="top-icons">
      <i class="fa-regular fa-message" style="transform: scaleX(-1);"></i>
      <i class="fa-solid fa-bag-shopping"></i>
      <i class="fa-regular fa-bell"></i>
      <div class="language-dropdown">
        <img src="https://flagcdn.com/w40/id.png" class="flag-icon" alt="Indonesian Flag" id="selected-flag">
        <div class="dropdown-content">
          <img src="https://flagcdn.com/w40/gb.png" alt="UK Flag" onclick="changeFlag('gb')">
          <img src="https://flagcdn.com/w40/id.png" alt="Indonesian Flag" onclick="changeFlag('id')">
        </div>
      </div>
      <i class="fa-regular fa-circle-user"></i>
    </div>
  </div>

  <div class="chat-container">
  <div class="main-chat">
    <!-- contoh chat header -->
    <div class="chat-header">
      <img src="https://i.pravatar.cc/150?img=12" alt="Avatar">
      {{ $receiver->name ?? 'Pengguna' }}
    </div>

    <div class="chat-body">
      {{-- Daftar pesan bisa kamu render di sini --}}
      @foreach($messages as $message)
        <div class="message-wrapper {{ $message->sender_id === auth()->id() ? 'outgoing' : 'incoming' }}">
          <img src="https://i.pravatar.cc/100?u={{ $message->sender_id }}" class="avatar">
          <div>
            <div class="message {{ $message->sender_id === auth()->id() ? 'outgoing' : 'incoming' }}">
              {{ $message->body }}
            </div>
            <span class="timestamp">{{ $message->created_at->format('H:i') }}</span>
          </div>
        </div>
      @endforeach

      {{-- Form Upload Bukti Pembayaran --}}
      @if(request()->has('order_id'))
        <form action="{{ route('chat.sendPaymentProof') }}" method="POST" enctype="multipart/form-data" style="margin-top: 30px; border-top: 1px solid #ccc; padding-top: 20px;">
          @csrf
          <input type="hidden" name="order_id" value="{{ request('order_id') }}">

          <label for="proof_image" style="font-weight: bold; margin-bottom: 5px; display: block;">Upload Bukti Pembayaran</label>
          <input type="file" name="proof_image" accept="image/*" required style="margin-bottom: 10px;">

          <button type="submit" style="background-color: #0B3D3B; color: white; padding: 8px 16px; border: none; border-radius: 5px; cursor: pointer;">
            Kirim Bukti Pembayaran
          </button>
        </form>
      @endif
    </div>

    <div class="message-input">
      <input type="text" placeholder="Ketik pesan...">
      <button class="icon-btn"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
</div>
    
  </div>

  <script>
    function changeFlag(code) {
      const flag = document.getElementById('selected-flag');
      flag.src = `https://flagcdn.com/w40/${code}.png`;
    }
  </script>
</body>
</html>
