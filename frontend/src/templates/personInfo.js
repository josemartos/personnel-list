const pluginUrl = window.wpSettings.pluginUrl;
const pl = "personnelList";

const socialNetworks = [
  {
    slug: "github",
    alt: "Github",
    title: "Visit Github profile"
  },
  {
    slug: "linkedin",
    alt: "Linkedin",
    title: "Visit Linkedin profile"
  },
  {
    slug: "xing",
    alt: "Xing",
    title: "Visit Xing profile"
  },
  {
    slug: "facebook",
    alt: "Facebook",
    title: "Visit Facebook profile"
  }
];
const getSocialIcon = async ({ url, network }) => {
  // "eager" generates no extra chunk
  const iconImage = await import(
    /* webpackMode: "eager" */ `../assets/svg/${network.slug}-icon.svg`
  );

  return `<a href="${url}" title="${network.title}">
    <img 
      src="${pluginUrl}/frontend/dist/${iconImage.default}" 
      alt="${network.alt}" 
    />
  </a>`;
};

const personInfoTemplate = async person => {
  let socialNetworksHtml = "";

  for (const network of socialNetworks) {
    const url = person[network.slug];
    socialNetworksHtml +=
      url && url.length ? await getSocialIcon({ url, network }) : "";
  }

  return `<div class="${pl}_item_template">
    <header>
      <img src="https://via.placeholder.com/89" alt=${person.name} />
      <h2 class="${pl}_item_template_title">
        ${person.name} <br />
        <span class="${pl}_item_template_position">
          ${person.position}
        </span>
      </h2>
    </header>
    
    <p class="${pl}_item_template_description">
      ${person.short_description}
    </p>
    <div class="${pl}_item_template_social">
      ${socialNetworksHtml}
    </div>
  </div>
`;
};

export default personInfoTemplate;
