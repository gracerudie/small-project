(() => {
  const API_BASE = 'http://localhost/small-project/LAMPAPI/'; // absolute path for endpoints

  // Elements
  const welcomeView = id('welcomeView');
  const authView = id('authView');
  const appView = id('appView');
  const loginForm = id('loginForm');
  const registerForm = id('registerForm');
  const addContactForm = id('addContactForm');
  const loginMsg = id('loginMessage');
  const registerMsg = id('registerMessage');
  const addMsg = id('addMessage');
  const searchMsg = id('searchMessage');
  const searchInput = id('searchInput');
  const tbody = qs('#contactsTable tbody');
  const emptyState = id('emptyState');
  const userGreeting = id('userGreeting');
  const logoutBtn = id('logoutBtn');

  const showRegisterBtn = id('showRegister');
  const showLoginBtn = id('showLogin');
  const goLoginBtn = id('goLogin');
  const goRegisterBtn = id('goRegister');
  const loginSection = id('loginSection');
  const registerSection = id('registerSection');

  let currentUser = null;
  let searchTimer = null;

  function id(x){ return document.getElementById(x); }
  function qs(sel,scope=document){ return scope.querySelector(sel); }
  function clear(el){ if(el) el.textContent=''; }
  function setMsg(el, msg, ok=false){ if(!el) return; el.textContent=msg; el.classList.toggle('success', !!ok); }

  async function api(endpoint, payload) {
    const res = await fetch(API_BASE + endpoint, {
      method: 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify(payload||{})
    });
    const data = await res.json().catch(()=>({error:'Invalid JSON'}));
    if(!res.ok || data.error){
      throw new Error(data.error || ('HTTP '+res.status));
    }
    return data;
  }

  // Auth
  async function handleLogin(e){
    e.preventDefault();
    setMsg(loginMsg, '');
    const login = val('login_login');
    const password = val('login_password');
    if(!login || !password){ return setMsg(loginMsg,'Enter credentials'); }
    try {
      const data = await api('Login.php', { login, password });
      currentUser = { id: data.id, firstName: data.firstName, lastName: data.lastName };
      persistUser();
      enterApp();
    } catch (err){
      setMsg(loginMsg, err.message);
    }
  }

  async function handleRegister(e){
    e.preventDefault();
    setMsg(registerMsg,'');
    const firstName = val('reg_first');
    const lastName = val('reg_last');
    const login = val('reg_login');
    const password = val('reg_password');
    if(!firstName||!lastName||!login||!password){ return setMsg(registerMsg,'All fields required'); }
    try {
      const data = await api('Register.php', { firstName, lastName, login, password });
      currentUser = { id: data.id, firstName: data.firstName, lastName: data.lastName };
      persistUser();
      enterApp();
    } catch (err){
      setMsg(registerMsg, err.message);
    }
  }

  function val(idStr){ return id(idStr).value.trim(); }

  function persistUser(){
    if(currentUser){
      localStorage.setItem('user', JSON.stringify(currentUser));
    }
  }

  function restoreUser(){
    try {
      const raw = localStorage.getItem('user');
      if(raw){
        const u = JSON.parse(raw);
        if(u && u.id) {
          currentUser = u;
          enterApp();
        }
      }
    } catch(e){}
  }

  function enterApp(){
    if(!currentUser) return;
    hideAllViews();
    appView.classList.remove('hidden');
    userGreeting.textContent = `Logged in as ${currentUser.firstName} ${currentUser.lastName}`;
    setMsg(addMsg,'');
    setMsg(searchMsg,'');
    loadContacts();
  }

  function hideAllViews(){
    welcomeView && welcomeView.classList.add('hidden');
    authView.classList.add('hidden');
    appView.classList.add('hidden');
  }

  function showWelcome(){
    hideAllViews();
    welcomeView && welcomeView.classList.remove('hidden');
  }

  function showAuth(mode='login'){
    hideAllViews();
    authView.classList.remove('hidden');
    if(mode==='register'){
      loginSection.classList.add('hidden');
      registerSection.classList.remove('hidden');
    } else {
      registerSection.classList.add('hidden');
      loginSection.classList.remove('hidden');
    }
    setMsg(loginMsg,'');
    setMsg(registerMsg,'');
  }

  function logout(){
    localStorage.removeItem('user');
    currentUser = null;
    showWelcome();
    loginForm.reset();
    registerForm.reset();
    addContactForm.reset();
    tbody.innerHTML='';
    emptyState.classList.add('hidden');
  }

  // Contacts
  async function loadContacts(term=''){
    if(!currentUser) return;
    try {
      setMsg(searchMsg, term ? 'Searching...' : '');
      const data = await api('SearchContacts.php', { userId: currentUser.id, search: term });
      renderContacts(data.results || []);
      setMsg(searchMsg,'',true);
    } catch(err){
      renderContacts([]);
      setMsg(searchMsg, err.message);
    }
  }

  function renderContacts(list){
    tbody.innerHTML = '';
    if(!list.length){
      emptyState.classList.remove('hidden');
      return;
    }
    emptyState.classList.add('hidden');
    const frag = document.createDocumentFragment();
    list.forEach(c => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${escapeHtml(c.FirstName)} ${escapeHtml(c.LastName)}</td>
        <td class="hide-sm">${escapeHtml(c.Phone)}</td>
        <td class="hide-sm">${escapeHtml(c.Email)}</td>
        <td>
          <button class="action-btn" data-id="${c.contactId}">Delete</button>
        </td>`;
      frag.appendChild(tr);
    });
    tbody.appendChild(frag);
  }

  async function handleAddContact(e){
    e.preventDefault();
    if(!currentUser) return;
    setMsg(addMsg,'Adding...');
    const firstName = val('add_first');
    const lastName = val('add_last');
    const phone = val('add_phone');
    const email = val('add_email');
    if(!firstName||!lastName||!phone||!email){
      return setMsg(addMsg,'All fields required');
    }
    try {
      await api('AddContact.php', { userId: currentUser.id, firstName, lastName, phone, email });
      addContactForm.reset();
      setMsg(addMsg,'Added', true);
      loadContacts(searchInput.value.trim());
    } catch(err){
      setMsg(addMsg, err.message);
    }
  }

  async function deleteContact(id){
    if(!currentUser) return;
    try {
      await api('DeleteContact.php', { userId: currentUser.id, contactId: id });
      loadContacts(searchInput.value.trim());
    } catch(err){
      setMsg(searchMsg, err.message);
    }
  }

  function debounceSearch(){
    clearTimeout(searchTimer);
    searchTimer = setTimeout(()=> {
      loadContacts(searchInput.value.trim());
    }, 350);
  }

  // Utilities
  function escapeHtml(str){
    return String(str||'').replace(/[&<>"']/g, s => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    }[s]));
  }

  // Event listeners
  loginForm.addEventListener('submit', handleLogin);
  registerForm.addEventListener('submit', handleRegister);
  addContactForm.addEventListener('submit', handleAddContact);
  logoutBtn.addEventListener('click', logout);

  showRegisterBtn.addEventListener('click', ()=> showAuth('register'));
  showLoginBtn.addEventListener('click', ()=> showAuth('login'));
  goLoginBtn && goLoginBtn.addEventListener('click', ()=> showAuth('login'));
  goRegisterBtn && goRegisterBtn.addEventListener('click', ()=> showAuth('register'));

  searchInput.addEventListener('input', debounceSearch);

  tbody.addEventListener('click', e=>{
    const btn = e.target.closest('button.action-btn');
    if(btn){
      const id = parseInt(btn.dataset.id,10);
      if(Number.isInteger(id)){
        if(confirm('Delete this contact?')){
          deleteContact(id);
        }
      }
    }
  });

  // Init
  document.addEventListener('DOMContentLoaded', () => {
    restoreUser();
    if(!currentUser){
      showWelcome();
    }
  });
})();
