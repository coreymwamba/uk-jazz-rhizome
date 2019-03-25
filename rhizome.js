var svg = d3.select("#rhizome");
    // width = +svg.attr("width"),
    // height = +svg.attr("height");
var width = 5000;
var height = 5000;
var dataQuery = svg.attr("data-query");

// horrible globvar but will do until the inevitable refactoring...
var links = null;

// var color = d3.scaleOrdinal(d3.schemeCategory10);
function color(type) {
  switch (type) {
  case "person":
    return "#f8efc6";
    break;
  case "group":
    return "#e7eef6";
    break;
  case "organisation":
    return "#cae9e3";
    break;
  default:
    return "#000";
  }
}

function size(type) {
  switch (type) {
  case "person":
    return "10";
    break;
  default:
    return "18";
  }
}
function rad(type) {
  switch (type) {
  case "person":
    return "40";
    break;
  default:
    return "50";
  }
}

function title(str) {
  return str.replace(/\b\S/g, function(t) { return t.toUpperCase() });
}

var node = null;
var min_zoom = 0.1;
var max_zoom = 6;

var link_colour_default = '#999';
var link_colour_highlight = 'red';

var jsonPull = "rhizome-json.php";
if (dataQuery) {
  jsonPull += "?"+dataQuery;
}

d3.json(jsonPull, function(error, graph) {
  if (error) throw error;
  var memberLinks = graph.links.memberOf;
  var alumLinks = graph.links.alumOf;

  links = d3.merge([memberLinks, alumLinks]);

  const gData = {
    nodes: graph.nodes,
    links: links
  }
  
  const Graph = ForceGraph3D()
    (document.getElementById('rhizome'))
      .graphData(gData)
      .nodeAutoColorBy('type')
      .nodeThreeObject(node => {
        // This block copied from @vasturiano's demo
        // use a sphere as a drag handle
        const obj = new THREE.Mesh(
          new THREE.SphereGeometry(10),
          new THREE.MeshBasicMaterial({ depthWrite: false, transparent: true, opacity: 0 })
        );
        // add text sprite as child
        const sprite = new SpriteText(node.name);
        sprite.color = node.color;
        sprite.textHeight = 8;
        obj.add(sprite);
        return obj;
      });;

  Graph.d3Force('charge').strength(-150);
});
