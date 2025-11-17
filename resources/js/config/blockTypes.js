// Block type configurations for Page Builder
export const BLOCK_TYPES = {
  TEXT: 'text',
  HEADING: 'heading',
  IMAGE: 'image',
  GALLERY: 'gallery',
  VIDEO: 'video',
  HTML: 'html',
  BUTTON: 'button',
  DIVIDER: 'divider',
  SPACER: 'spacer',
  CONTAINER: 'container',
  COLUMNS: 'columns',
};

// Block configurations with metadata
export const BLOCK_CONFIGS = {
  [BLOCK_TYPES.TEXT]: {
    type: 'text',
    label: 'Text Block',
    icon: 'text',
    description: 'Rich text content with formatting',
    category: 'content',
    defaultContent: {
      text: '<p>Start typing...</p>',
      format: 'html',
    },
    defaultProperties: {
      textAlign: 'left',
      fontSize: 'base',
      color: 'default',
    },
  },
  [BLOCK_TYPES.HEADING]: {
    type: 'heading',
    label: 'Heading',
    icon: 'heading',
    description: 'Page headings (H1-H6)',
    category: 'content',
    defaultContent: {
      text: 'Heading Text',
      level: 2,
    },
    defaultProperties: {
      textAlign: 'left',
      color: 'default',
    },
  },
  [BLOCK_TYPES.IMAGE]: {
    type: 'image',
    label: 'Image',
    icon: 'image',
    description: 'Single image with caption',
    category: 'media',
    defaultContent: {
      src: '',
      alt: '',
      caption: '',
    },
    defaultProperties: {
      width: '100%',
      objectFit: 'cover',
      align: 'center',
    },
  },
  [BLOCK_TYPES.GALLERY]: {
    type: 'gallery',
    label: 'Gallery',
    icon: 'images',
    description: 'Multiple images in a grid',
    category: 'media',
    defaultContent: {
      images: [],
    },
    defaultProperties: {
      columns: 3,
      gap: '1rem',
      aspectRatio: '16/9',
    },
  },
  [BLOCK_TYPES.VIDEO]: {
    type: 'video',
    label: 'Video',
    icon: 'video',
    description: 'Embedded video (YouTube, Vimeo, etc.)',
    category: 'media',
    defaultContent: {
      url: '',
      provider: 'youtube',
      autoplay: false,
    },
    defaultProperties: {
      aspectRatio: '16/9',
      controls: true,
    },
  },
  [BLOCK_TYPES.HTML]: {
    type: 'html',
    label: 'Custom HTML',
    icon: 'code',
    description: 'Custom HTML/CSS/JavaScript',
    category: 'advanced',
    defaultContent: {
      html: '<div>Custom HTML</div>',
    },
    defaultProperties: {
      sanitize: true,
    },
  },
  [BLOCK_TYPES.BUTTON]: {
    type: 'button',
    label: 'Button',
    icon: 'button',
    description: 'Call-to-action button',
    category: 'interactive',
    defaultContent: {
      text: 'Click me',
      link: '',
      openInNewTab: false,
    },
    defaultProperties: {
      variant: 'primary',
      size: 'medium',
      align: 'left',
      fullWidth: false,
    },
  },
  [BLOCK_TYPES.DIVIDER]: {
    type: 'divider',
    label: 'Divider',
    icon: 'divider',
    description: 'Horizontal line separator',
    category: 'layout',
    defaultContent: {},
    defaultProperties: {
      style: 'solid',
      thickness: '1px',
      color: '#e5e7eb',
      spacing: '2rem',
    },
  },
  [BLOCK_TYPES.SPACER]: {
    type: 'spacer',
    label: 'Spacer',
    icon: 'spacer',
    description: 'Vertical spacing',
    category: 'layout',
    defaultContent: {},
    defaultProperties: {
      height: '2rem',
    },
  },
  [BLOCK_TYPES.CONTAINER]: {
    type: 'container',
    label: 'Container',
    icon: 'container',
    description: 'Group blocks together',
    category: 'layout',
    defaultContent: {
      blocks: [],
    },
    defaultProperties: {
      maxWidth: '1200px',
      padding: '1rem',
      backgroundColor: 'transparent',
    },
  },
  [BLOCK_TYPES.COLUMNS]: {
    type: 'columns',
    label: 'Columns',
    icon: 'columns',
    description: 'Multi-column layout',
    category: 'layout',
    defaultContent: {
      columns: [
        { blocks: [], width: '50%' },
        { blocks: [], width: '50%' },
      ],
    },
    defaultProperties: {
      gap: '1rem',
      verticalAlign: 'top',
    },
  },
};

// Block categories for grouping in palette
export const BLOCK_CATEGORIES = {
  CONTENT: {
    id: 'content',
    label: 'Content',
    icon: 'file-text',
  },
  MEDIA: {
    id: 'media',
    label: 'Media',
    icon: 'image',
  },
  INTERACTIVE: {
    id: 'interactive',
    label: 'Interactive',
    icon: 'cursor-click',
  },
  LAYOUT: {
    id: 'layout',
    label: 'Layout',
    icon: 'layout',
  },
  ADVANCED: {
    id: 'advanced',
    label: 'Advanced',
    icon: 'code',
  },
};

// Get blocks by category
export function getBlocksByCategory(category) {
  return Object.values(BLOCK_CONFIGS).filter(
    block => block.category === category
  );
}

// Get block config by type
export function getBlockConfig(type) {
  return BLOCK_CONFIGS[type] || null;
}

// Create new block data
export function createBlockData(type, customContent = {}, customProperties = {}) {
  const config = getBlockConfig(type);
  if (!config) {
    throw new Error(`Unknown block type: ${type}`);
  }

  return {
    type,
    name: config.label,
    content: { ...config.defaultContent, ...customContent },
    properties: { ...config.defaultProperties, ...customProperties },
    is_visible: true,
  };
}
