import express from "express";
import { spawn } from "child_process";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();

// Serve static files (CSS, JS, images)
app.use(express.static(__dirname));

// PHP handler
app.all("/*.php", (req, res) => {
  const php = spawn("php-cgi", ["-f", path.join(__dirname, req.path)]);
  let output = "";

  php.stdout.on("data", (data) => (output += data));
  php.stderr.on("data", (data) => console.error(data.toString()));

  php.on("close", () => {
    res.send(output);
  });
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`✅ Running on port ${PORT}`));
