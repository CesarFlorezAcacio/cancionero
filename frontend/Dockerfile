FROM denoland/deno:2.9.8

WORKDIR /app

COPY deno.json ./
RUN deno install

COPY . .

EXPOSE 5173
CMD ["task", "dev"]
