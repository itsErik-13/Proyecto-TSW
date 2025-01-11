Handlebars.registerHelper('if_eq', function(a, b, opts) {
  if (a == b)
    return opts.fn(this);
  else
    return opts.inverse(this);
});

Handlebars.registerHelper('if_neq', function(a, b, opts) {
  if (a != b)
    return opts.fn(this);
  else
    return opts.inverse(this);
});

Handlebars.registerHelper('contains', function(value, array, options) {
  if (Array.isArray(array) && array.includes(value)) {
    return options.fn(this); // Renderiza el bloque si se encuentra el valor
  } else {
    return options.inverse(this); // Renderiza el bloque {{else}} si no se encuentra
  }
});