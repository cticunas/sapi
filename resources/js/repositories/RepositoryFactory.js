import PlanRepository from './PlanRepository'
import JournalRepository from './JournalRepository'
import PersonRepository from './PersonRepository'
import RenacytRepository from './RenacytRepository'
import UserRepository from './UserRepository'
import CategoryRepository from './CategoryRepository'
import OrganizationRepository from './OrganizationRepository'
import ResearchRepository from './ResearchRepository'
import AuthorRepository from './AuthorRepository'
import OutcomeRepository from './OutcomeRepository'
import FileRepository from './FileRepository'
import MasterRepository from './MasterRepository'
import DocumentRepository from './DocumentRepository'
import EventRepository from './EventRepository'

const repositories ={
    plan: PlanRepository,
    journal: JournalRepository,
    person: PersonRepository,
    renacyt: RenacytRepository,
    user: UserRepository,
    category: CategoryRepository,
    organization: OrganizationRepository,
    research: ResearchRepository,
    author: AuthorRepository,
    outcome: OutcomeRepository,
    file: FileRepository,
    master: MasterRepository,
    document: DocumentRepository,
    event: EventRepository,
    //mas repos
}

export default {
    get: name => repositories[name]
}