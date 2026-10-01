import { Head, router } from '@inertiajs/react';
import { Scale, Users } from 'lucide-react';
import { useState } from 'react';
import { CommitteeTabs } from '@/components/committee-tabs';
import { Button } from '@/components/ui/button';
import { PageHeader } from '@/components/ui/misc';
import { AuthorityCheck } from '@/pages/committees/authority-check';

type Option = { value: number | string; label: string };

type Props = {
    members: { total: number; withoutNik: number };
    productOptions: Option[];
    conditionMap: Record<string, string[]>;
    committeeMembers: Option[];
};

const RULES = [
    'An applicant is recognised by their national ID (NIK) when the credit is requested. A file can also be flagged by hand.',
    'Nobody takes part in deciding their own file. When the level that would decide is the applicant’s, the level above decides.',
    'If the top committee member is the applicant, the highest level below decides, and the file is marked as an exception.',
    'If the applicant’s role has another holder (such as the second section head), that person acts and the level stays.',
    'The applicant can never be the section head or the surveyor of their own file.',
    'Scope: the applicant only. Close family is on the backlog.',
];

export default function CommitteeExceptions({
    members,
    productOptions,
    conditionMap,
    committeeMembers,
}: Props) {
    const [checking, setChecking] = useState(false);

    return (
        <>
            <Head title="Committee exceptions" />
            <PageHeader
                title="Committees"
                description="When the applicant is a committee member"
                actions={
                    <>
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/committees/members')}
                        >
                            <Users /> Members
                        </Button>
                        <Button onClick={() => setChecking(true)}>
                            <Scale /> Check authority
                        </Button>
                    </>
                }
            />
            <CommitteeTabs current="exceptions" />

            <ul className="border-border flex list-disc flex-col gap-1.5 rounded-lg border bg-surface py-3 pr-3 pl-7 text-sm text-muted">
                {RULES.map((rule) => (
                    <li key={rule}>{rule}</li>
                ))}
            </ul>
            <p className="mt-3 text-sm text-muted">
                {members.total} committee{' '}
                {members.total === 1 ? 'member' : 'members'} ·{' '}
                {members.withoutNik} without a NIK on record. Use Check
                authority and pick the applicant to see how a file would be
                decided.
            </p>

            <AuthorityCheck
                open={checking}
                onOpenChange={setChecking}
                productOptions={productOptions}
                conditionMap={conditionMap}
                memberOptions={committeeMembers}
            />
        </>
    );
}
