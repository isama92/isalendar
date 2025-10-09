const providerIcons = import.meta.glob('../../images/providers/*', {
    eager: true,
    import: 'default',
});

const ProviderLogo = ({ image_path, alt, className }) => {
    const logo = providerIcons[`../../images/providers/${image_path}`];

    return <img alt={alt} src={logo} className={className} />;
};

export default ProviderLogo;
