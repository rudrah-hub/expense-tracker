const axios = require("axios");

// MCP endpoint (Laravel)
const MCP_URL = "http://127.0.0.1:8000/mcp/expense-tracker";

// Ollama endpoint
const OLLAMA_URL = "http://localhost:11434/api/chat";

// Step 1: fetch MCP tools
async function getTools() {
  const res = await axios.post(MCP_URL, {
    jsonrpc: "2.0",
    id: 1,
    method: "tools/list"
  });

  return res.data.result.tools;
}

// Step 2: call MCP tool
async function callTool(name, args) {
  const res = await axios.post(MCP_URL, {
    jsonrpc: "2.0",
    id: 2,
    method: "tools/call",
    params: {
      name,
      arguments: args
    }
  });

  return res.data.result;
}

// Step 3: talk to Ollama
async function chatWithOllama(prompt, tools) {
  return axios.post("http://localhost:11434/api/chat", {
    model: "qwen2.5:7b",
    messages: [
      { role: "user", content: prompt }
    ],
    tools,
    stream: false
  });
}

(async () => {
  // Fetch MCP tools
  const tools = await getTools();

  // Ask Ollama
  const message = await chatWithOllama(
    "Add food expense of 120 rupees",
    tools
  );

  // If Ollama wants to call a tool
  if (message.tool_calls?.length) {
    for (const call of message.tool_calls) {
      const result = await callTool(
        call.name,
        call.arguments
      );

      console.log("Tool result:", result);
    }
  } else {
    console.log("LLM response:", message.content);
  }
})();
