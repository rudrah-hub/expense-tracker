const axios = require("axios");

const MCP_URL = "http://127.0.0.1:8000/mcp/expense-tracker";
const OLLAMA_URL = "http://localhost:11434/api/chat";
const MODEL = "qwen2.5:7b";

async function getMcpTools() {
  const res = await axios.post(MCP_URL, {
    jsonrpc: "2.0",
    id: 1,
    method: "tools/list"
  });

  return res.data.result.tools;
}

async function callMcpTool(name, args) {
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

function convertMcpToolsToOllama(mcpTools) {
  return mcpTools.map(tool => ({
    type: "function",
    function: {
      name: tool.name
        .replace(/-tool$/, "")
        .replace(/-/g, "_"),

      description: tool.description || "",

      parameters: {
        type: "object",
        properties: tool.inputSchema?.properties || {},
        required: Object.keys(tool.inputSchema?.properties || {})
      }
    }
  }));
}

async function chat(prompt, tools) {
  const res = await axios.post(OLLAMA_URL, {
    model: MODEL,
    messages: [{ role: "user", content: prompt }],
    tools,
    stream: false
  });

  return res.data;
}

(async () => {
  console.log("Fetching MCP tools...");
  const mcpTools = await getMcpTools();

  console.log("Converting tools for Ollama...");
  const ollamaTools = convertMcpToolsToOllama(mcpTools);

  console.log("Asking model...");
  const result = await chat(
    "What is my total expense this year?",
    ollamaTools
  );

  const message = result.message;

  if (message.tool_calls?.length) {
    for (const call of message.tool_calls) {
      const mcpToolName =
        call.function.name.replace(/_/g, "-") + "-tool";

      console.log("Calling MCP tool:", mcpToolName);
      console.log("Arguments:", call.function.arguments);

      const toolResult = await callMcpTool(
        mcpToolName,
        call.function.arguments
      );

      console.log("Tool result:", toolResult);
    }
  } else {
    console.log("LLM:", message.content);
  }
})();
