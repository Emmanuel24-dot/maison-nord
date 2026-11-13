window.MN_PRODUCTS = [
  { id: "manteau-camille", name: "Manteau Camille", category: "Femme", price: 149000, image: "assets/clothing-1.jpg",
    short: "Manteau long en laine vierge, coupe oversize et tombé fluide.",
    long: "Le manteau Camille incarne notre vision du vestiaire essentiel. Coupé dans une laine vierge italienne, il se distingue par son tombé fluide et sa silhouette oversize qui s'adapte à toutes les saisons.",
    features: ["Laine vierge italienne", "Doublure satin", "Boutons en corozo", "Coupe oversize"],
    composition: "90% laine vierge, 10% cachemire — Doublure 100% viscose",
    sizes: ["XS","S","M","L","XL"], colors: ["Beige","Camel","Noir"], badge: "Nouveau" },
  { id: "chemise-auguste", name: "Chemise Auguste", category: "Homme", price: 75000, image: "assets/clothing-2.jpg",
    short: "Chemise en lin lavé, col français, finitions soignées.",
    long: "Une chemise pensée pour durer. Le lin lavé apporte douceur et tenue, le col français se ferme avec une élégance discrète, et chaque finition est travaillée pour traverser les années.",
    features: ["Lin européen","Col français","Coutures rabattues","Poche poitrine"],
    composition: "100% lin lavé", sizes: ["S","M","L","XL"], colors: ["Marine","Blanc","Sable"] },
  { id: "pull-lila", name: "Pull Lila", category: "Femme", price: 89000, image: "assets/clothing-3.jpg",
    short: "Pull en cachemire côtelé, col rond, maille épaisse.",
    long: "Tricoté en Italie dans un cachemire de Mongolie intérieure, le pull Lila offre une chaleur enveloppante sans le poids. Sa maille côtelée structure la silhouette tout en restant douce au porter.",
    features: ["Cachemire de Mongolie","Tricoté en Italie","Maille côtelée","Côtes aux poignets"],
    composition: "100% cachemire", sizes: ["XS","S","M","L"], colors: ["Crème","Anthracite","Bordeaux"], badge: "Populaire" },
  { id: "robe-solea", name: "Robe Soléa", category: "Femme", price: 105000, image: "assets/clothing-4.jpg",
    short: "Robe midi en soie naturelle, manches ballons, ceinture marquée.",
    long: "Une robe d'été qui se porte en toute occasion. La soie naturelle apporte un mouvement aérien, les manches ballons adoucissent les épaules et la ceinture marque la taille avec élégance.",
    features: ["Soie naturelle","Manches ballons","Ceinture marquée","Doublure coton"],
    composition: "100% soie — Doublure 100% coton biologique", sizes: ["XS","S","M","L"], colors: ["Terracotta","Ivoire","Noir"] },
  { id: "sac-madeleine", name: "Sac Madeleine", category: "Accessoires", price: 125000, image: "assets/clothing-5.jpg",
    short: "Cabas en cuir pleine fleur, anses tressées, grande capacité.",
    long: "Le sac Madeleine est un compagnon du quotidien. Façonné dans un cuir pleine fleur tanné végétalement, il développe une patine unique au fil du temps et accompagne aussi bien le bureau que le week-end.",
    features: ["Cuir pleine fleur","Tannage végétal","Grande capacité","Anses tressées"],
    composition: "100% cuir de vachette pleine fleur", sizes: ["Unique"], colors: ["Caramel","Noir","Cognac"] },
  { id: "pantalon-leon", name: "Pantalon Léon", category: "Homme", price: 69000, image: "assets/clothing-6.jpg",
    short: "Pantalon de tailleur en laine, coupe droite, pinces avant.",
    long: "Un pantalon de tailleur intemporel coupé dans une laine froide italienne. La coupe droite et les pinces avant structurent la silhouette pour un effet ajusté sans rigidité.",
    features: ["Laine froide italienne","Pinces avant","Coupe droite","Doublure poches"],
    composition: "98% laine, 2% élasthanne", sizes: ["38","40","42","44","46"], colors: ["Noir","Anthracite","Marine"] }
];

/* Format price in FCFA with non-breaking space as thousands separator */
window.MN_FORMAT_PRICE = function (n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' FCFA';
};

window.MN_GET_PRODUCT = function (id) {
  return window.MN_PRODUCTS.find(function (p) { return p.id === id; });
};

