import fs from 'node:fs';
import * as topojson from 'topojson-client';
import { geoMercator, geoPath } from 'd3-geo';
const world = JSON.parse(fs.readFileSync('node_modules/world-atlas/countries-110m.json'));
const countries = topojson.feature(world, world.objects.countries);
// The Americas, by ISO numeric ids used by world-atlas (Natural Earth)
const americas = new Set(['124','840','484','084','188','222','320','340','558','591','032','068','076','152','170','218','254','328','600','604','740','858','862','044','052','060','192','212','214','308','312','332','388','474','500','531','533','534','535','630','659','662','670','780','796','850','028','092','136','184']);
const feats = countries.features.filter(f => americas.has(f.id));
const W = 620, H = 820;
const projection = geoMercator().rotate([90, 0]).fitSize([W, H], { type: 'FeatureCollection', features: feats });
projection.clipExtent([[36, 0], [W, H]]);
const path = geoPath(projection);
let d = '';
for (const f of feats) { const p = path(f); if (p) d += p; }
const pts = { calgary: [-114.07, 51.05], grandePrairie: [-118.80, 55.17], redDeer: [-113.81, 52.27], midland: [-102.08, 31.99], mosquera: [-74.23, 4.71] };
const pos = Object.fromEntries(Object.entries(pts).map(([k, v]) => { const [x, y] = projection(v); return [k, { x: +x.toFixed(1), y: +y.toFixed(1), px: +(x / W * 100).toFixed(2), py: +(y / H * 100).toFixed(2) }]; }));
const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${W} ${H}" width="${W}" height="${H}" role="img" aria-label="Map of North and South America"><path fill="#d6d7d9" d="${d}"/></svg>`;
fs.writeFileSync('../wp/wp-content/themes/protorque/assets/img/map-americas.svg', svg);
fs.writeFileSync('map-points.json', JSON.stringify(pos, null, 2));
console.log('svg KB', (svg.length / 1024).toFixed(0)); console.log(pos);
