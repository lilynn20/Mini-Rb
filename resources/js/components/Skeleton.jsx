export function CardSkeleton() {
    return (
        <div className="animate-pulse">
            <div className="aspect-square bg-gray-200 rounded-xl mb-3" />
            <div className="h-4 bg-gray-200 rounded w-1/3 mb-2" />
            <div className="h-3 bg-gray-200 rounded w-2/3 mb-2" />
            <div className="h-4 bg-gray-200 rounded w-1/2" />
        </div>
    );
}

export function CardGridSkeleton({ count = 8 }) {
    return (
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            {Array.from({ length: count }).map((_, i) => <CardSkeleton key={i} />)}
        </div>
    );
}

export function ShowPageSkeleton() {
    return (
        <div className="animate-pulse max-w-5xl mx-auto px-8 py-10">
            <div className="h-8 bg-gray-200 rounded w-2/3 mb-4" />
            <div className="h-4 bg-gray-200 rounded w-1/3 mb-8" />
            <div className="h-[500px] bg-gray-200 rounded-2xl mb-10" />
            <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div className="md:col-span-2 space-y-3">
                    <div className="h-6 bg-gray-200 rounded w-1/2" />
                    <div className="h-3 bg-gray-200 rounded w-3/4" />
                    <div className="h-3 bg-gray-200 rounded w-2/3" />
                    <div className="h-3 bg-gray-200 rounded w-3/4" />
                </div>
                <div className="border rounded-2xl p-6 space-y-3">
                    <div className="h-6 bg-gray-200 rounded w-1/2" />
                    <div className="h-10 bg-gray-200 rounded" />
                    <div className="h-10 bg-gray-200 rounded" />
                </div>
            </div>
        </div>
    );
}

export function ListSkeleton({ rows = 4 }) {
    return (
        <div className="animate-pulse space-y-4">
            {Array.from({ length: rows }).map((_, i) => (
                <div key={i} className="bg-white rounded-2xl border p-6 flex gap-6">
                    <div className="w-36 h-28 bg-gray-200 rounded-xl flex-shrink-0" />
                    <div className="flex-1 space-y-3">
                        <div className="h-5 bg-gray-200 rounded w-1/2" />
                        <div className="h-4 bg-gray-200 rounded w-1/3" />
                        <div className="h-4 bg-gray-200 rounded w-1/4" />
                    </div>
                </div>
            ))}
        </div>
    );
}
