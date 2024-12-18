const { registerBlockType } = wp.blocks;
const { SelectControl } = wp.components;
const { useState, useEffect } = wp.element;
const { __ } = wp.i18n;
const { useBlockProps, BlockControls } = wp.blockEditor;

registerBlockType('simple-language-switcher/translatable-string', {
    apiVersion: 3,
    
    edit: function Edit(props) {
        const { attributes, setAttributes } = props;
        const [strings, setStrings] = useState(null);
        const blockProps = useBlockProps({
            draggable: true
        });
        
        useEffect(() => {
            wp.apiFetch({ 
                path: '/simple-language-switcher/v1/strings'
            }).then(fetchedStrings => {
                setStrings(fetchedStrings);
            });
        }, []);

        if (!strings) {
            return wp.element.createElement('div', 
                { ...blockProps },
                __('Loading...', 'simple-language-switcher')
            );
        }

        if (strings.length === 0) {
            return wp.element.createElement('div',
                { ...blockProps },
                __('No translatable strings found. Please add some in the Settings > Translatable Strings page.', 'simple-language-switcher')
            );
        }

        const options = strings.map(string => ({
            label: string.value,
            value: string.identifier
        }));

        options.unshift({ 
            label: __('Select a string', 'simple-language-switcher'), 
            value: '' 
        });

        return wp.element.createElement(
            wp.element.Fragment,
            null,
            [
                wp.element.createElement(BlockControls, { key: 'controls' }),
                wp.element.createElement('div', 
                    { 
                        ...blockProps,
                        className: `${blockProps.className} sls-translatable-string-block`
                    },
                    [
                        wp.element.createElement(SelectControl, {
                            key: 'select',
                            label: __('Select Translatable String', 'simple-language-switcher'),
                            value: attributes.identifier,
                            options: options,
                            onChange: (identifier) => setAttributes({ identifier })
                        }),
                        attributes.identifier && wp.element.createElement('div',
                            { 
                                key: 'preview',
                                className: 'sls-preview'
                            },
                            strings.find(s => s.identifier === attributes.identifier)?.value
                        )
                    ]
                )
            ]
        );
    },

    save: function Save() {
        return null;
    }
});
