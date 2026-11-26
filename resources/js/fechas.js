export const zonaHorariaBolivia = 'America/La_Paz';

const formatoFechaHora = new Intl.DateTimeFormat('es-BO', {
  timeZone: zonaHorariaBolivia,
  day: '2-digit',
  month: '2-digit',
  year: 'numeric',
  hour: '2-digit',
  minute: '2-digit',
  hourCycle: 'h23',
});

export function parseFechaApi(valor) {
  // Laravel entrega algunas marcas de tiempo de la base de datos sin zona.
  const fechaUtc = /^(\d{4}-\d{2}-\d{2})[ T](\d{2}:\d{2}(?::\d{2}(?:\.\d+)?)?)$/;
  return new Date(valor.replace(fechaUtc, '$1T$2Z'));
}

export function formatFecha(valor) {
  if (!valor) return '';
  const fecha = parseFechaApi(valor);
  return Number.isNaN(fecha.getTime()) ? '' : formatoFechaHora.format(fecha);
}
