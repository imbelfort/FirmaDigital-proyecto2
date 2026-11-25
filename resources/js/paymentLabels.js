export const paymentMethods = {
  PAGOFACIL_QR: 'QR · PagoFácil',
  STRIPE: 'Stripe',
  MANUAL_VENTANILLA: 'Ventanilla',
};

export const paymentStatuses = {
  COMPLETADO: 'Acreditado',
  PENDIENTE: 'Pendiente',
  PROCESANDO: 'Procesando',
  RECHAZADO: 'Tarjeta rechazada',
  FALLIDO: 'Fallido',
  CANCELADO: 'Cancelado',
  EXPIRADO: 'Vencido',
  SIN_INICIAR: 'Sin iniciar',
};

export function paymentStatus(payment) {
  if (!payment) return 'SIN_INICIAR';
  if (payment.metodo_pago === 'PAGOFACIL_QR' && payment.estado === 'PENDIENTE'
    && payment.fecha_expiracion && new Date(payment.fecha_expiracion) <= new Date()) return 'EXPIRADO';
  return payment.estado;
}
