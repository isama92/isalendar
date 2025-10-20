const providerIcons = import.meta.glob('../../images/providers/*', {
    eager: true,
    import: 'default',
});

const ProviderLogo = ({ image_path, title, className }) => {
    const logo = providerIcons[`../../images/providers/${image_path}`];

    return <img alt={title} title={title} src={logo} className={className} />;
};

export default ProviderLogo;
