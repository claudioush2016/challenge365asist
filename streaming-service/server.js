const express = require("express");
const http = require("http");
const { Server } = require("socket.io");
const Redis = require("ioredis");

const app = express();
const server = http.createServer(app);

// Configura Socket.IO para permitir conexiones desde cualquier origen (CORS)
const io = new Server(server, {
  cors: {
    origin: "*",
  },
});

// Crea una instancia de cliente para la suscripción
const redisSubscriber = new Redis({
  host: "127.0.0.1",
  port: 6379,
});

const channelName = "events:bookings";

// Suscríbete al canal de Redis que usa Laravel
redisSubscriber.subscribe(channelName, (err, count) => {
  if (err) {
    console.error("Error al suscribirse al canal de Redis:", err);
  } else {
    console.log(`Subscribed to ${count} channel(s).`);
  }
});

// Escucha los mensajes que provienen de Laravel a través de Redis
redisSubscriber.on("message", (channel, message) => {
  console.log(`Mensaje recibido del canal "${channel}":`, message);
  try {
    const data = JSON.parse(message);
    // Ahora el JSON es más simple, por lo que no necesitas data.data
    io.emit("bookingUpdate", data); // <-- ¡Cambia esta línea!
  } catch (error) {
    console.error("Error al parsear el mensaje JSON:", error);
  }
});

// Maneja las conexiones de los clientes de Socket.IO (el dashboard)
io.on("connection", (socket) => {
  console.log("Un cliente se ha conectado.");
  socket.on("disconnect", () => {
    console.log("Un cliente se ha desconectado.");
  });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log(`Servicio de streaming corriendo en http://localhost:${PORT}`);
});
