
const Index = () => import('./components/l-limitless-bs4/Index');
const Form = () => import('./components/l-limitless-bs4/Form');
const Show = () => import('./components/l-limitless-bs4/Show');
const SideBarLeft = () => import('./components/l-limitless-bs4/SideBarLeft');
const SideBarRight = () => import('./components/l-limitless-bs4/SideBarRight');

const routes = [

    {
        path: '/purchase-orders',
        components: {
            default: Index,
            //'sidebar-left': ComponentSidebarLeft,
            //'sidebar-right': ComponentSidebarRight
        },
        meta: {
            title: 'Accounting :: Sales :: Purchase Orders',
            metaTags: [
                {
                    name: 'description',
                    content: 'Purchase Orders'
                },
                {
                    property: 'og:description',
                    content: 'Purchase Orders'
                }
            ]
        }
    },
    {
        path: '/purchase-orders/create',
        components: {
            default: Form,
            //'sidebar-left': ComponentSidebarLeft,
            //'sidebar-right': ComponentSidebarRight
        },
        meta: {
            title: 'Accounting :: Sales :: Purchase Order :: Create',
            metaTags: [
                {
                    name: 'description',
                    content: 'Create Purchase Order'
                },
                {
                    property: 'og:description',
                    content: 'Create Purchase Order'
                }
            ]
        }
    },
    {
        path: '/purchase-orders/:id',
        components: {
            default: Show,
            'sidebar-left': SideBarLeft,
            'sidebar-right': SideBarRight
        },
        meta: {
            title: 'Accounting :: Sales :: Purchase Order',
            metaTags: [
                {
                    name: 'description',
                    content: 'Purchase Order'
                },
                {
                    property: 'og:description',
                    content: 'Purchase Order'
                }
            ]
        }
    },
    {
        path: '/purchase-orders/:id/copy',
        components: {
            default: Form,
        },
        meta: {
            title: 'Accounting :: Sales :: Purchase Order :: Copy',
            metaTags: [
                {
                    name: 'description',
                    content: 'Copy Purchase Order'
                },
                {
                    property: 'og:description',
                    content: 'Copy Purchase Order'
                }
            ]
        }
    },
    {
        path: '/purchase-orders/:id/edit',
        components: {
            default: Form,
        },
        meta: {
            title: 'Accounting :: Sales :: Purchase Order :: Edit',
            metaTags: [
                {
                    name: 'description',
                    content: 'Edit Purchase Order'
                },
                {
                    property: 'og:description',
                    content: 'Edit Purchase Order'
                }
            ]
        }
    }

]

export default routes
