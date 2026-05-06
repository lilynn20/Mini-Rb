import { useEffect, useRef, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import api from '../api';
import Layout from '../components/Layout';

const formatTime = (d) =>
    new Date(d).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

const formatDate = (d) =>
    new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });

export default function Messages() {
    const [searchParams, setSearchParams] = useSearchParams();
    const initialPartnerId = searchParams.get('to');

    const [conversations, setConversations] = useState([]);
    const [activeId, setActiveId] = useState(initialPartnerId ? Number(initialPartnerId) : null);
    const [conversation, setConversation] = useState(null);
    const [draft, setDraft] = useState('');
    const [sending, setSending] = useState(false);
    const messagesEndRef = useRef(null);

    const loadConversations = () => api.get('/messages').then((res) => setConversations(res.data));
    const loadConversation = (uid) =>
        api.get(`/messages/${uid}`).then((res) => setConversation(res.data));

    useEffect(() => {
        loadConversations();
        const interval = setInterval(() => {
            loadConversations();
            if (activeId) loadConversation(activeId);
        }, 5000);
        return () => clearInterval(interval);
    }, [activeId]);

    useEffect(() => {
        if (activeId) loadConversation(activeId);
    }, [activeId]);

    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [conversation?.messages?.length]);

    const handleSelect = (uid) => {
        setActiveId(uid);
        const params = new URLSearchParams(searchParams);
        params.set('to', String(uid));
        setSearchParams(params, { replace: true });
    };

    const handleSend = async (e) => {
        e.preventDefault();
        if (!draft.trim() || !activeId) return;
        setSending(true);
        try {
            const { data: msg } = await api.post(`/messages/${activeId}`, { content: draft });
            setConversation((prev) => ({
                ...prev,
                messages: [...(prev?.messages || []), msg],
            }));
            setDraft('');
            loadConversations();
        } finally {
            setSending(false);
        }
    };

    return (
        <Layout>
            <main className="max-w-6xl mx-auto px-8 py-10">
                <h1 className="text-3xl font-bold mb-8">Messagerie</h1>

                <div className="bg-white rounded-2xl shadow-sm border overflow-hidden grid grid-cols-1 md:grid-cols-3 h-[600px]">
                    <div className="border-r overflow-y-auto">
                        {conversations.length === 0 ? (
                            <div className="p-6 text-center text-gray-400 text-sm">
                                Aucune conversation pour l'instant.
                            </div>
                        ) : (
                            conversations.map((c) => (
                                <button
                                    key={c.user.id}
                                    type="button"
                                    onClick={() => handleSelect(c.user.id)}
                                    className={`w-full text-left p-4 border-b hover:bg-gray-50 transition flex items-center gap-3 ${
                                        activeId === c.user.id ? 'bg-rose-50' : ''
                                    }`}
                                >
                                    <div className="w-10 h-10 rounded-full overflow-hidden bg-rose-100 flex items-center justify-center text-rose-500 font-bold flex-shrink-0">
                                        {c.user.avatar_url ? (
                                            <img src={c.user.avatar_url} alt="" className="w-full h-full object-cover" />
                                        ) : (
                                            c.user.name.charAt(0).toUpperCase()
                                        )}
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <div className="flex justify-between items-baseline">
                                            <p className="font-semibold truncate">{c.user.name}</p>
                                            {c.last_message && (
                                                <span className="text-xs text-gray-400 ml-2 flex-shrink-0">
                                                    {formatDate(c.last_message.created_at)}
                                                </span>
                                            )}
                                        </div>
                                        {c.last_message && (
                                            <p className="text-sm text-gray-500 truncate">
                                                {c.last_message.is_mine && 'Vous: '}
                                                {c.last_message.content}
                                            </p>
                                        )}
                                    </div>
                                    {c.unread > 0 && (
                                        <span className="bg-rose-500 text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0">
                                            {c.unread}
                                        </span>
                                    )}
                                </button>
                            ))
                        )}
                    </div>

                    <div className="md:col-span-2 flex flex-col">
                        {!activeId ? (
                            <div className="flex-1 flex items-center justify-center text-gray-400">
                                Sélectionnez une conversation
                            </div>
                        ) : !conversation ? (
                            <div className="flex-1 flex items-center justify-center text-gray-400">Chargement...</div>
                        ) : (
                            <>
                                <div className="p-4 border-b flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-full overflow-hidden bg-rose-100 flex items-center justify-center text-rose-500 font-bold">
                                        {conversation.partner.avatar_url ? (
                                            <img src={conversation.partner.avatar_url} alt="" className="w-full h-full object-cover" />
                                        ) : (
                                            conversation.partner.name.charAt(0).toUpperCase()
                                        )}
                                    </div>
                                    <p className="font-semibold">{conversation.partner.name}</p>
                                </div>

                                <div className="flex-1 overflow-y-auto p-4 space-y-2">
                                    {conversation.messages.length === 0 ? (
                                        <p className="text-center text-gray-400 text-sm">
                                            Aucun message — commencez la conversation !
                                        </p>
                                    ) : (
                                        conversation.messages.map((m) => (
                                            <div
                                                key={m.id}
                                                className={`flex ${m.is_mine ? 'justify-end' : 'justify-start'}`}
                                            >
                                                <div
                                                    className={`max-w-[75%] px-4 py-2 rounded-2xl ${
                                                        m.is_mine
                                                            ? 'bg-rose-500 text-white rounded-br-sm'
                                                            : 'bg-gray-100 text-gray-800 rounded-bl-sm'
                                                    }`}
                                                >
                                                    <p className="whitespace-pre-line break-words">{m.content}</p>
                                                    <p className={`text-[10px] mt-1 ${m.is_mine ? 'text-rose-100' : 'text-gray-400'}`}>
                                                        {formatTime(m.created_at)}
                                                    </p>
                                                </div>
                                            </div>
                                        ))
                                    )}
                                    <div ref={messagesEndRef} />
                                </div>

                                <form onSubmit={handleSend} className="p-4 border-t flex gap-2">
                                    <input
                                        type="text"
                                        value={draft}
                                        onChange={(e) => setDraft(e.target.value)}
                                        placeholder="Écrivez un message..."
                                        className="flex-1 px-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-rose-500"
                                        disabled={sending}
                                    />
                                    <button
                                        type="submit"
                                        disabled={sending || !draft.trim()}
                                        className="bg-rose-500 text-white px-6 py-2 rounded-full font-semibold hover:bg-rose-600 transition disabled:opacity-50"
                                    >
                                        Envoyer
                                    </button>
                                </form>
                            </>
                        )}
                    </div>
                </div>
            </main>
        </Layout>
    );
}
