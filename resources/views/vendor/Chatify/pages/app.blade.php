@include('Chatify::layouts.headLinks')
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
      margin-left: 167px;
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

<div class="navbar">
  <h1><a href="/dashboard" style="color: inherit; text-decoration: none;">SisaRasa</a></h1>
    <div class="navbar-center">
        <a href="/dashboard"><span>Beranda</span></a>
        <a href="#menu"><span>Menu</span></a>
        <a href="#lokasi"><span>Lokasi</span></a>
        <a href="#riwayat"><span>Riwayat</span></a>      
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
<div class="messenger">
    {{-- ----------------------Users/Groups lists side---------------------- --}}
    <div class="messenger-listView {{ !!$id ? 'conversation-active' : '' }}">
        {{-- Header and search bar --}}
        <div class="m-header">
            <nav>
              <a href="/chatify" style="color: #0B3D3B;">
                <i class="fas fa-inbox"></i>
                <span class="messenger-headTitle">BincangRasa</span>
              </a>              
                {{-- header buttons --}}
                <nav class="m-header-right">
                    <a href="#"><i class="fas fa-cog settings-btn"></i></a>
                    <a href="#" class="listView-x"><i class="fas fa-times"></i></a>
                </nav>
            </nav>
            {{-- Search input --}}
            <input type="text" class="messenger-search" placeholder="Search" />
            {{-- Tabs --}}
            {{-- <div class="messenger-listView-tabs">
                <a href="#" class="active-tab" data-view="users">
                    <span class="far fa-user"></span> Contacts</a>
            </div> --}}
        </div>
        {{-- tabs and lists --}}
        <div class="m-body contacts-container">
           {{-- Lists [Users/Group] --}}
           {{-- ---------------- [ User Tab ] ---------------- --}}
           <div class="show messenger-tab users-tab app-scroll" data-view="users">
               {{-- Favorites --}}
               <div class="favorites-section">
                <p class="messenger-title"><span>Favorites</span></p>
                <div class="messenger-favorites app-scroll-hidden"></div>
               </div>
               {{-- Saved Messages --}}
               <p class="messenger-title"><span>Your Space</span></p>
               {!! view('Chatify::layouts.listItem', ['get' => 'saved']) !!}
               {{-- Contact --}}
               <p class="messenger-title"><span>All Messages</span></p>
               <div class="listOfContacts" style="width: 100%;height: calc(100% - 272px);position: relative;"></div>
           </div>
             {{-- ---------------- [ Search Tab ] ---------------- --}}
           <div class="messenger-tab search-tab app-scroll" data-view="search">
                {{-- items --}}
                <p class="messenger-title"><span>Search</span></p>
                <div class="search-records">
                    <p class="message-hint center-el"><span>Type to search..</span></p>
                </div>
             </div>
        </div>
    </div>

    {{-- ----------------------Messaging side---------------------- --}}
    <div class="messenger-messagingView">
        {{-- header title [conversation name] amd buttons --}}
        <div class="m-header m-header-messaging">
            <nav class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                {{-- header back button, avatar and user name --}}
                <div class="chatify-d-flex chatify-justify-content-between chatify-align-items-center">
                    <a href="#" class="show-listView"><i class="fas fa-arrow-left"></i></a>
                    <div class="avatar av-s header-avatar" style="margin: 0px 10px; margin-top: -5px; margin-bottom: -5px;">
                    </div>
                    <a href="#" class="user-name">{{ config('chatify.name') }}</a>
                </div>
                {{-- header buttons --}}
                <nav class="m-header-right">
                    <a href="#" class="add-to-favorite"><i class="fas fa-star"></i></a>
                    <a href="/"><i class="fas fa-home"></i></a>
                    <a href="#" class="show-infoSide"><i class="fas fa-info-circle"></i></a>
                </nav>
            </nav>
            {{-- Internet connection --}}
            <div class="internet-connection">
                <span class="ic-connected">Connected</span>
                <span class="ic-connecting">Connecting...</span>
                <span class="ic-noInternet">No internet access</span>
            </div>
        </div>

        {{-- Messaging area --}}
        <div class="m-body messages-container app-scroll">
            <div class="messages">
                <p class="message-hint center-el"><span>Please select a chat to start messaging</span></p>
            </div>
            {{-- Typing indicator --}}
            <div class="typing-indicator">
                <div class="message-card typing">
                    <div class="message">
                        <span class="typing-dots">
                            <span class="dot dot-1"></span>
                            <span class="dot dot-2"></span>
                            <span class="dot dot-3"></span>
                        </span>
                    </div>
                </div>
            </div>

        </div>
        {{-- Send Message Form --}}
        @include('Chatify::layouts.sendForm')
    </div>
    {{-- ---------------------- Info side ---------------------- --}}
    <div class="messenger-infoView app-scroll">
        {{-- nav actions --}}
        <nav>
            <p>User Details</p>
            <a href="#"><i class="fas fa-times"></i></a>
        </nav>
        {!! view('Chatify::layouts.info')->render() !!}
    </div>
</div>

@include('Chatify::layouts.modals')
@include('Chatify::layouts.footerLinks')
