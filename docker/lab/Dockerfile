# Imagen base: nginx sobre Alpine (minúscula, ~50 MB, arranque instantáneo)
FROM nginx:alpine

# Directorio de trabajo dentro del contenedor
WORKDIR /usr/share/nginx/html

# Copiamos nuestro contenido. El primer "." es relativo al WORKDIR
COPY index.html .

# Documenta el puerto. NO lo publica, solo informa a quien lea la imagen
EXPOSE 80

# Proceso principal (PID 1). Sin "daemon off;" nginx arrancaría y moriría
CMD ["nginx", "-g", "daemon off;"]

