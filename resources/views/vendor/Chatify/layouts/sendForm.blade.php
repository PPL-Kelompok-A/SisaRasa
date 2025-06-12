<div class="messenger-sendCard">
    <!-- UPLOAD FILE - SANGAT SEDERHANA -->
    <div style="background: white; padding: 20px; margin: 10px; border: 3px solid red;">
        <h3 style="color: red; text-align: center;">UPLOAD BUKTI PEMBAYARAN</h3>

        <input type="file" accept="image/*,.pdf" style="width: 100%; height: 50px; font-size: 18px; padding: 10px; border: 2px solid red; background: yellow;">

        <p style="text-align: center; color: red; font-weight: bold;">KLIK DI ATAS UNTUK PILIH FILE</p>
    </div>

    <form id="message-form" method="POST" action="{{ route('send.message') }}" enctype="multipart/form-data">
        @csrf
        <label><span class="fas fa-plus-circle"></span><input type="file" class="upload-attachment" name="file" accept=".{{implode(', .',config('chatify.attachments.allowed_images'))}}, .{{implode(', .',config('chatify.attachments.allowed_files'))}}" /></label>
        <button class="emoji-button"><span class="fas fa-smile"></span></button>
        <textarea name="message" class="m-send app-scroll" placeholder="Type a message.."></textarea>
        <button class="send-button"><span class="fas fa-paper-plane"></span></button>
    </form>
</div>




