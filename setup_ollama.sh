#!/bin/bash

# --- Configuration ---
USER_ID="m263222"              # Replace with your USNA Midshipman/Faculty ID
REMOTE_HOST="lnx1084335govt" # Replace with the specific GPU machine address
# ---------------------

echo "🚀 Connecting to $REMOTE_HOST as $USER_ID..." 

# We pass a 'heredoc' block of commands to execute on the remote machine
ssh -t "${USER_ID}@${REMOTE_HOST}" << 'EOF'
    echo "Checking for Ollama..."
    
    if ! command -v ollama &> /dev/null; then
        echo "Ollama not found. Installing..."
        curl -fsSL https://ollama.com/install.sh | sh
    else
        echo "Ollama is already installed."
    fi

    # Start the Ollama serve process in the background if it's not running
    if ! pgrep -x "ollama" > /dev/null; then
        echo "Starting Ollama service..."
        nohup ollama serve > ollama.log 2>&1 &
        sleep 5
    fi

    echo "Verifying GPU availability..."
    nvidia-smi
    
    echo "--- Environment Ready ---"
    ollama --version
    # Optional: Pull a model immediately
    # ollama pull llama3
EOF

echo "✅ Setup script complete."