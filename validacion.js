document.getElementById("cotizacion-form").addEventListener("submit", function(event) {
  event.preventDefault(); // Evita el envío automático del formulario

  let marca = document.getElementById("marca").value;
  let anio = document.getElementById("anio").value;
  let ciudad = document.getElementById("ciudad").value.trim();
  let cobertura = document.getElementById("cobertura").value;
  let mensaje = document.getElementById("mensaje-validacion");

  // Validaciones
  if (!marca || !anio || !ciudad || !cobertura) {
      mensaje.textContent = "Todos los campos son obligatorios.";
      return;
  }

  if (anio < 2000 || anio > 2025) {
      mensaje.textContent = "El año del vehículo debe estar entre 2000 y 2025.";
      return;
  }

  this.submit();
});
