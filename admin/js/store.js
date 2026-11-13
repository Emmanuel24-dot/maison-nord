const AdminStore = (() => {
  function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
  }

  function jsonHeaders() {
    return { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf() };
  }

  async function req(url, opts = {}) {
    const r = await fetch(url, opts);
    const data = await r.json();
    if (!r.ok) throw new Error(data.error || `Erreur ${r.status}`);
    return data;
  }

  return {
    load:   (key)      => req(`api/${key}.php`),
    create: (key, d)   => req(`api/${key}.php`, { method: 'POST',   headers: jsonHeaders(), body: JSON.stringify(d) }),
    update: (key, id, d) => req(`api/${key}.php?id=${encodeURIComponent(id)}`, { method: 'PUT', headers: jsonHeaders(), body: JSON.stringify(d) }),
    remove: (key, id)  => req(`api/${key}.php?id=${encodeURIComponent(id)}`, { method: 'DELETE', headers: jsonHeaders() }),
  };
})();
