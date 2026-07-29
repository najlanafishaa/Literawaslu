<!-- Floating Action Button (FAB) Ajukan Pertanyaan -->
<div id="fabQuestionContainer" style="position: fixed; bottom: 24px; right: 24px; z-index: 99999; font-family: 'Plus Jakarta Sans', sans-serif;">
    <!-- FAB Button -->
    <button type="button" id="fabQuestionBtn" onclick="toggleQuestionModal(true)" 
        style="display: flex; align-items: center; gap: 10px; padding: 12px 20px; background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%); color: #ffffff; border: none; border-radius: 50px; font-weight: 600; font-size: 14px; box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4); cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none;"
        onmouseover="this.style.transform='translateY(-4px) scale(1.03)'; this.style.boxShadow='0 12px 30px rgba(220, 38, 38, 0.5)';"
        onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 8px 25px rgba(220, 38, 38, 0.4)';">
        <div style="width: 32px; height: 32px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <i class="fa-solid fa-comments" style="font-size: 16px;"></i>
        </div>
        <span style="letter-spacing: 0.2px;">Ajukan Pertanyaan</span>
    </button>

    <!-- Modal Form Pertanyaan -->
    <div id="questionModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 100000; align-items: center; justify-content: center; padding: 16px; opacity: 0; transition: opacity 0.3s ease;">
        <div id="questionModalCard" style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
            
            <!-- Modal Header -->
            <div style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); padding: 20px 24px; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 38px; height: 38px; background: rgba(255,255,255,0.15); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-circle-question" style="font-size: 20px; color: #ffffff;"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 17px; font-weight: 700; color: #ffffff;">Ajukan Pertanyaan</h3>
                        <p style="margin: 2px 0 0 0; font-size: 12px; color: rgba(255,255,255,0.8);">Kami siap membantu menjawab pertanyaan Anda</p>
                    </div>
                </div>
                <button type="button" onclick="toggleQuestionModal(false)" style="background: transparent; border: none; color: rgba(255,255,255,0.8); font-size: 20px; cursor: pointer; padding: 4px 8px; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.color='#fff'; this.style.background='rgba(255,255,255,0.1)';" onmouseout="this.style.color='rgba(255,255,255,0.8)'; this.style.background='transparent';">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div style="padding: 24px;">
                <!-- Alert Box untuk error / validasi -->
                <div id="questionModalAlert" style="display: none; padding: 12px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 16px;"></div>

                <form id="fabQuestionForm" onsubmit="submitQuestionForm(event)">
                    @csrf
                    <div style="margin-bottom: 16px;">
                        <label for="fab_name" style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                        <div style="position: relative;">
                            <i class="fa-solid fa-user" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                            <input type="text" id="fab_name" name="name" required placeholder="Masukkan nama lengkap Anda" 
                                value="{{ auth()->check() ? auth()->user()->name : '' }}"
                                style="width: 100%; padding: 10px 14px 10px 38px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #1e293b; outline: none; transition: border-color 0.2s;"
                                onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cbd5e1'">
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label for="fab_email" style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Alamat Email <span style="color: #ef4444;">*</span></label>
                        <div style="position: relative;">
                            <i class="fa-solid fa-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                            <input type="email" id="fab_email" name="email" required placeholder="contoh@email.com" 
                                value="{{ auth()->check() ? auth()->user()->email : '' }}"
                                style="width: 100%; padding: 10px 14px 10px 38px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #1e293b; outline: none; transition: border-color 0.2s;"
                                onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cbd5e1'">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label for="fab_message" style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Pesan Pertanyaan <span style="color: #ef4444;">*</span></label>
                        <div style="position: relative;">
                            <textarea id="fab_message" name="message" rows="4" required placeholder="Tuliskan pertanyaan Anda secara jelas..." 
                                style="width: 100%; padding: 10px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; color: #1e293b; outline: none; transition: border-color 0.2s; resize: vertical; min-height: 90px;"
                                onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cbd5e1'"></textarea>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div style="display: flex; gap: 12px; justify-content: flex-end; align-items: center;">
                        <button type="button" onclick="toggleQuestionModal(false)" 
                            style="padding: 10px 18px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s;"
                            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#ffffff'">
                            Batal
                        </button>
                        <button type="submit" id="fabSubmitBtn"
                            style="display: flex; align-items: center; gap: 8px; padding: 10px 22px; background: #dc2626; color: #ffffff; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s;"
                            onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                            <span id="fabSubmitBtnText">Kirim Pertanyaan</span>
                            <i id="fabSubmitIcon" class="fa-solid fa-paper-plane"></i>
                            <i id="fabSpinnerIcon" class="fa-solid fa-circle-notch fa-spin" style="display: none;"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleQuestionModal(show) {
        const modal = document.getElementById('questionModal');
        const card = document.getElementById('questionModalCard');
        const alertBox = document.getElementById('questionModalAlert');

        if (show) {
            alertBox.style.display = 'none';
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 10);
        } else {
            modal.style.opacity = '0';
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    }

    async function submitQuestionForm(e) {
        e.preventDefault();
        const form = document.getElementById('fabQuestionForm');
        const alertBox = document.getElementById('questionModalAlert');
        const submitBtn = document.getElementById('fabSubmitBtn');
        const submitText = document.getElementById('fabSubmitBtnText');
        const submitIcon = document.getElementById('fabSubmitIcon');
        const spinnerIcon = document.getElementById('fabSpinnerIcon');

        // Loading state
        submitBtn.disabled = true;
        submitText.innerText = 'Mengirim...';
        submitIcon.style.display = 'none';
        spinnerIcon.style.display = 'inline-block';
        alertBox.style.display = 'none';

        const formData = new FormData(form);

        try {
            const response = await fetch('{{ route("questions.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            let data = {};
            const textResponse = await response.text();
            try {
                data = JSON.parse(textResponse);
            } catch (err) {
                console.error('Non-JSON response:', textResponse);
                // Extract brief message from HTML if present
                const cleanText = textResponse.replace(/<[^>]*>?/gm, '').substring(0, 150).trim();
                throw new Error(cleanText || 'Server mengembalikan respon tidak valid.');
            }

            if (response.ok && data.success) {
                // Tampilkan konfirmasi sukses di modal / Toast jika ada
                if (typeof showToast === 'function') {
                    showToast(data.message, 'success');
                }
                
                alertBox.style.background = '#f0fdf4';
                alertBox.style.border = '1px solid #bbf7d0';
                alertBox.style.color = '#166534';
                alertBox.innerHTML = '<i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> ' + data.message;
                alertBox.style.display = 'block';

                form.reset();

                setTimeout(() => {
                    toggleQuestionModal(false);
                }, 3500);
            } else {
                let errorMsg = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                alertBox.style.background = '#fef2f2';
                alertBox.style.border = '1px solid #fecaca';
                alertBox.style.color = '#991b1b';
                alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> ' + errorMsg;
                alertBox.style.display = 'block';
            }
        } catch (error) {
            console.error('Submit question error:', error);
            alertBox.style.background = '#fef2f2';
            alertBox.style.border = '1px solid #fecaca';
            alertBox.style.color = '#991b1b';
            alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> Gagal menghubungkan ke server: ' + (error.message || 'Terjadi kesalahan jaringan.');
            alertBox.style.display = 'block';
        } finally {
            submitBtn.disabled = false;
            submitText.innerText = 'Kirim Pertanyaan';
            submitIcon.style.display = 'inline-block';
            spinnerIcon.style.display = 'none';
        }
    }
</script>
