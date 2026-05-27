document.addEventListener('DOMContentLoaded', () => {
    // Elements
    const modeBtns = document.querySelectorAll('.mode-btn');
    const mainBtn = document.getElementById('mainActionBtn');
    const formGroupMessage = document.getElementById('formGroupMessage');
    const messageInput = document.getElementById('messageInput');
    const passphraseInput = document.getElementById('passphraseInput');
    const layersInput = document.getElementById('layersInput');
    const layersVal = document.getElementById('layersVal');
    const togglePassphraseBtn = document.getElementById('togglePassphraseBtn');
    const resultContainer = document.getElementById('resultContainer');
    const resultOutput = document.getElementById('resultOutput');
    const copyBtn = document.getElementById('copyBtn');
    const statusMsg = document.getElementById('statusMsg');
    const loader = document.getElementById('loader');
    const glassPanel = document.querySelector('.glass-panel');

    // State
    let currentMode = 'encrypt'; // 'encrypt' or 'decrypt'
    const API_URL = "https://secretletter-api.onrender.com/Backend/Controller/Controller.php";

    // Update layers display
    layersInput.addEventListener('input', (e) => {
        layersVal.textContent = e.target.value;
    });

    // Toggle Mode
    modeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modeBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentMode = btn.dataset.mode;
            
            // Update UI based on mode
            resultContainer.style.display = 'none';
            statusMsg.style.display = 'none';
            messageInput.value = '';
            passphraseInput.value = '';
            
            if (currentMode === 'encrypt') {
                document.querySelector('label[for="messageInput"]').textContent = 'YOUR MESSAGE';
                messageInput.placeholder = 'Enter the secret you wish to encrypt...';
                mainBtn.textContent = 'INITIATE ENCRYPTION';
                mainBtn.classList.remove('decrypt-mode');
                glassPanel.classList.remove('decrypt-mode-active');
            } else {
                document.querySelector('label[for="messageInput"]').textContent = 'ENCRYPTED PAYLOAD';
                messageInput.placeholder = 'Paste the encrypted string here...';
                mainBtn.textContent = 'INITIATE DECRYPTION';
                mainBtn.classList.add('decrypt-mode');
                glassPanel.classList.add('decrypt-mode-active');
            }
        });
    });

    // Auto resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Toggle passphrase visibility
    togglePassphraseBtn.addEventListener('click', () => {
        const visible = passphraseInput.type === 'text';
        passphraseInput.type = visible ? 'password' : 'text';
        togglePassphraseBtn.textContent = visible ? '👁️' : '🙈';
    });

    // Main Action (API Call)
    mainBtn.addEventListener('click', async () => {
        const msg = messageInput.value.trim();
        const pass = passphraseInput.value.trim();
        const layers = layersInput.value;

        if (!msg || !pass) {
            showError("System Error: Message and Passphrase are required parameters.");
            return;
        }

        // Prepare payload
        const payload = {
            passphrase: pass,
            times: layers,
            mode: currentMode
        };

        if (currentMode === 'encrypt') {
            payload.message = msg;
        } else if(currentMode==='decrypt') {
            payload.secret = msg;
        }

        // Show Loader
        statusMsg.style.display = 'none';
        resultContainer.style.display = 'none';
        loader.classList.add('active');
        document.querySelector('.cyber-loader').textContent = currentMode === 'encrypt' ? 'Encrypting Data' : 'Decrypting Payload';

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.status === 'success') {
                resultOutput.value = data.response;
                resultContainer.style.display = 'block';
                
                // Auto resize result output
                resultOutput.style.height = 'auto';
                resultOutput.style.height = (resultOutput.scrollHeight) + 'px';
                
                // Scroll to result
                resultContainer.scrollIntoView({ behavior: 'smooth' });
            } else {
                showError("Decryption failed. Invalid passphrase or payload integrity compromised."
                );
            }
        } catch (error) {
            console.error(error);
            showError("Network anomaly detected. Failed to connect to encryption server.");
        } finally {
            loader.classList.remove('active');
        }
    });

    // Copy to Clipboard
    copyBtn.addEventListener('click', () => {
        resultOutput.select();
        navigator.clipboard.writeText(resultOutput.value).then(() => {
            const originalText = copyBtn.innerHTML;
            copyBtn.innerHTML = '✓ COPIED TO CLIPBOARD';
            copyBtn.style.color = currentMode === 'encrypt' ? 'var(--neon-green)' : 'var(--neon-purple)';
            copyBtn.style.borderColor = currentMode === 'encrypt' ? 'var(--neon-green)' : 'var(--neon-purple)';
            
            setTimeout(() => {
                copyBtn.innerHTML = originalText;
                copyBtn.style.color = '';
                copyBtn.style.borderColor = '';
            }, 2000);
        });
    });

    function showError(message) {
        statusMsg.textContent = message;
        statusMsg.className = 'status-msg status-error';
        statusMsg.style.display = 'block';
    }
});
